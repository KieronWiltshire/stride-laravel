<?php

namespace Domain\User;

use Domain\Permission\Permission;
use Domain\Role\Role;
use Domain\User\Events\EmailVerificationTokenGeneratedEvent;
use Domain\User\Contracts\Repositories\UserRepository;
use Domain\User\Events\PasswordResetTokenGeneratedEvent;
use Domain\User\Events\UserEmailVerifiedEvent;
use Domain\User\Events\UserPasswordResetEvent;
use Domain\User\Exceptions\InvalidEmailVerificationTokenException;
use Domain\User\Exceptions\InvalidPasswordResetTokenException;
use Domain\User\Exceptions\PasswordResetTokenExpiredException;
use Domain\User\Mail\EmailVerificationToken;
use Domain\User\Mail\PasswordResetToken;
use Domain\User\Validators\UserEmailValidator;
use Domain\User\Validators\UserPasswordValidator;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserService
{
  /**
   * @var \Domain\User\Contracts\Repositories\UserRepository
   */
  protected $userRepository;

  /**
   * @var \Domain\User\\Validators\UserEmailValidator
   */
  protected $userEmailValidator;

  /**
   * @var \Domain\User\\Validators\UserPasswordValidator
   */
  protected $userPasswordValidator;

  /**
   * Create a new user service instance.
   *
   * @param \Domain\User\Contracts\Repositories\UserRepository $userRepository
   * @param \Domain\User\Validators\UserEmailValidator $userEmailValidator
   * @param \Domain\User\Validators\UserPasswordValidator $userPasswordValidator
   */
  public function __construct(
    UserRepository $userRepository,
    UserEmailValidator $userEmailValidator,
    UserPasswordValidator $userPasswordValidator
  ) {
    $this->userRepository = $userRepository;
    $this->userEmailValidator = $userEmailValidator;
    $this->userPasswordValidator = $userPasswordValidator;
  }

  /**
   * Retrieve all of the users.
   *
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\User\\User>
   */
  public function all()
  {
    return $this->userRepository->all();
  }

  /**
   * Create a new user.
   *
   * @param array $attributes
   * @return \Domain\User\User
   *
   * @throws \Domain\User\Exceptions\CannotCreateUserException
   */
  public function create($attributes)
  {
    if (isset($attributes['email'])) {
      $attributes['email_verification_token'] = $this->generateEmailVerificationToken($attributes['email']);
    }

    return $this->userRepository->create($attributes);
  }

  /**
   * Create a user if the specified search parameters could not find one
   * with the matching criteria.
   *
   * @param number|string $parameter
   * @param number|string $search
   * @param boolean $regex
   * @param array $attributes
   * @return \Domain\User\User
   *
   * @throws \Domain\User\Exceptions\CannotCreateUserException
   */
  public function firstOrCreate($parameter, $search, $regex = true, $attributes = [])
  {
    if (isset($attributes['email'])) {
      $attributes['email_verification_token'] = $this->generateEmailVerificationToken($attributes['email']);
    }

    return $this->userRepository->firstOrCreate($parameter, $search, $regex, $attributes);
  }

  /**
   * Find a user by an unknown parameter.
   *
   * @param number|string $parameter
   * @param number|string $search
   * @param boolean $regex
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\User\User>
   */
  public function find($parameter, $search, $regex = true)
  {
    return $this->userRepository->find($parameter, $search, $regex);
  }

  /**
   * Find a user by identifier.
   *
   * @param string $id
   * @return \Domain\User\User
   *
   * @throws \Domain\User\Exceptions\UserNotFoundException
   */
  public function findById($id)
  {
    return $this->userRepository->findById($id);
  }

  /**
   * Find a user by email.
   *
   * @param string $email
   * @return \Domain\User\User
   *
   * @throws \Domain\User\Exceptions\UserNotFoundException
   */
  public function findByEmail($email)
  {
    return $this->userRepository->findByEmail($email);
  }

  /**
   * Update a user.
   *
   * @param \Domain\User\User $user
   * @param array $attributes
   * @return \Domain\User\User
   *
   * @throws \Domain\User\Exceptions\CannotUpdateUserException
   */
  public function update(User $user, $attributes)
  {
    return $this->userRepository->update($user, $attributes);
  }

  /**
   * Retrieve all of the users.
   *
   * @param integer $limit
   * @param integer $offset
   * @return \Illuminate\Pagination\LengthAwarePaginator<\Domain\User\User>
   *
   * @throws \Infrastructure\Exceptions\Pagination\InvalidPaginationException
   */
  public function index($limit = null, $offset = 1)
  {
    return $this->userRepository->paginate($limit, $offset)->all();
  }

  /**
   * Search for users with the specified search parameters.
   *
   * @param number|string $parameter
   * @param number|string $search
   * @param boolean $regex
   * @param integer $limit
   * @param integer $offset
   * @return \Illuminate\Pagination\LengthAwarePaginator<\Domain\User\User>
   */
  function search($parameter, $search, $regex = true, $limit = null, $offset = 1)
  {
    return $this->userRepository->paginate($limit, $offset)->find($parameter, $search, $regex);
  }

  /**
   * Router a new email verification token be generated with
   * the user's new email address to verify.
   *
   * @param \Domain\User\User $user
   * @param string $email
   * @return \Domain\User\User
   *
   * @throws \Domain\User\Exceptions\InvalidEmailException
   */
  public function requestEmailChange(User $user, $email)
  {
    $this->userEmailValidator->validate([
      'email' => $email
    ]);

    $attributes = [
      'email_verification_token' => $this->generateEmailVerificationToken($email)
    ];

    if ($this->userRepository->update($user, $attributes)) {
      event(new EmailVerificationTokenGeneratedEvent($user));

      return $user;
    }
  }

  /**
   * Verify the user's specified email address and set their
   * email to the new one encoded within the token.
   *
   * @param \Domain\User\User $user
   * @param string $emailVerificationToken
   * @return \Domain\User\User
   *
   * @throws \Domain\User\Exceptions\InvalidEmailException
   * @throws \Domain\User\Exceptions\InvalidEmailVerificationTokenException
   */
  public function verifyEmail(User $user, $emailVerificationToken)
  {
    $decodedToken = $this->decodeEmailVerificationToken($emailVerificationToken);

    if ($emailVerificationToken && $decodedToken && $user->email_verification_token == $emailVerificationToken) {
      $this->userEmailValidator->validate([
        'email' => $decodedToken->email
      ]);

      $oldEmail = $user->email;

      $attributes = [
        'email' => $decodedToken->email,
        'email_verified_at' => now(),
        'email_verification_token' => null,
      ];

      if ($this->userRepository->update($user, $attributes)) {
        event(new UserEmailVerifiedEvent($user, $oldEmail));

        return $user;
      }
    } else {
      throw new InvalidEmailVerificationTokenException();
    }
  }

  /**
   * Generate an email verification token for the specified email address.
   *
   * @param string $email
   * @return string
   */
  public function generateEmailVerificationToken($email)
  {
    return base64_encode(json_encode([
      'email' => $email,
      'token' => Str::random(32) // used to append randomness
    ]));
  }

  /**
   * Decode an email verification token.
   *
   * @param string $emailVerificationToken
   * @return Object
   */
  public function decodeEmailVerificationToken($emailVerificationToken)
  {
    return json_decode(base64_decode($emailVerificationToken));
  }

  /**
   * Send the email verification email.
   *
   * @param \Domain\User\User $user
   * @return void
   *
   * @throws \Domain\User\Exceptions\InvalidEmailVerificationTokenException
   */
  public function sendEmailVerificationToken(User $user)
  {
    if ($user->email_verification_token) {
      $decodedToken = $this->decodeEmailVerificationToken($user->email_verification_token);

      if ($decodedToken) {
        Mail::to($decodedToken->email)->send(new EmailVerificationToken($user->email_verification_token));
      } else {
        throw new Exception();
      }
    } else {
      throw new InvalidEmailVerificationTokenException();
    }
  }

  /**
   * Create's a password reset token for the specified user.
   *
   * @param \Domain\User\User $user
   * @return \Domain\User\User
   */
  public function forgotPassword(User $user)
  {
    $attributes = [
      'password_reset_token' => $this->generatePasswordResetToken(),
    ];

    if ($this->userRepository->update($user, $attributes)) {
      event(new PasswordResetTokenGeneratedEvent($user));

      return $user;
    }
  }

  /**
   * Reset a user's password using the password reset token.
   *
   * @param \Domain\User\User $user
   * @param string $password
   * @param string $passwordResetToken
   * @return \Domain\User\User
   *
   * @throws \Domain\User\Exceptions\InvalidPasswordException
   * @throws \Domain\User\Exceptions\PasswordResetTokenExpiredException
   * @throws \Domain\User\Exceptions\InvalidPasswordResetTokenException
   */
  public function resetPassword(User $user, $password, $passwordResetToken)
  {
    $decodedToken = $this->decodePasswordResetToken($passwordResetToken);

    if ($passwordResetToken && $decodedToken && $user->password_reset_token == $passwordResetToken) {
      if (Carbon::now()->lessThan(new Carbon($decodedToken->expiry))) {
        $this->userPasswordValidator->validate([
          'password' => $password
        ]);

        $attributes = [
          'password' => $password,
          'password_reset_token' => null,
        ];

        if ($this->$this->userRepository->update($user, $attributes)) {
          event(new UserPasswordResetEvent($user));

          return $user;
        }
      } else {
        throw new PasswordResetTokenExpiredException();
      }
    } else {
      throw new InvalidPasswordResetTokenException();
    }
  }

  /**
   * Generate a password reset token.
   *
   * @return string
   */
  public function generatePasswordResetToken()
  {
    return base64_encode(json_encode([
      'expiry' => Carbon::now()->addMinutes(config('auth.passwords.users.expire', 60)),
      'token' => Str::random(32) // used to append randomness
    ]));
  }

  /**
   * Decode a password reset token.
   *
   * @param string $passwordResetToken
   * @return Object
   */
  public function decodePasswordResetToken($passwordResetToken)
  {
    return json_decode(base64_decode($passwordResetToken));
  }

  /**
   * Send the user a password reset email.
   *
   * @param \Domain\User\User $user
   * @return void
   */
  public function sendPasswordResetToken(User $user)
  {
    if ($user->password_reset_token) {
      Mail::to($user->email)->send(new PasswordResetToken($user->password_reset_token));
    }
  }

  /**
   * Add a role to the user.
   *
   * @param \Domain\User\User $user
   * @param \Domain\Role\Role $role
   * @return \Domain\User\User
   */
  public function addRole(User $user, Role $role)
  {
    return $this->userRepository->addRole($user, $role);
  }

  /**
   * Add roles to the user.
   *
   * @param \Domain\User\User $user
   * @param array $roles
   * @return \Domain\User\User
   */
  public function addRoles(User $user, array $roles = [])
  {
    return $this->userRepository->addRoles($user, $roles);
  }

  /**
   * Remove a role from the user.
   *
   * @param \Domain\User\User $user
   * @param \Domain\Role\Role $role
   * @return \Domain\User\User
   */
  public function removeRole(User $user, Role $role)
  {
    return $this->userRepository->removeRole($user, $role);
  }

  /**
   * Remove roles from the user.
   *
   * @param \Domain\User\User $user
   * @param array $roles
   * @return \Domain\User\User
   */
  public function removeRoles(User $user, array $roles = [])
  {
    return $this->userRepository->removeRoles($user, $roles);
  }

  /**
   * Set all of the roles of the specified user.
   *
   * @param \Domain\User\User $user
   * @param array $roles
   * @return \Domain\User\User
   */
  public function setRoles(User $user, array $roles = [])
  {
    return $this->userRepository->setRoles($user, $roles);
  }

  /**
   * Retrieve all of the roles for the specified user.
   *
   * @param \Domain\User\User $user
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Role\Role>
   */
  function getRoles(User $user)
  {
    return $this->userRepository->getRoles($user);
  }

  /**
   * Add a permission to the specified user.
   *
   * @param \Domain\User\User $user
   * @param \Domain\Permission\Permission $permission
   * @return \Domain\User\User
   */
  public function addPermission(User $user, Permission $permission)
  {
    return $this->userRepository->addPermission($user, $permission);
  }

  /**
   * Add multiple permissions to the specified user.
   *
   * @param \Domain\User\User $user
   * @param array $permissions
   * @return \Domain\User\User
   */
  public function addPermissions(User $user, array $permissions = [])
  {
    return $this->userRepository->addPermissions($user, $permissions);
  }

  /**
   * Remove a permission from the specified user.
   *
   * @param \Domain\User\User $user
   * @param \Domain\Permission\Permission $permission
   * @return \Domain\User\User
   */
  public function removePermission(User $user, Permission $permission)
  {
    return $this->userRepository->removePermission($user, $permission);
  }

  /**
   * Remove multiple permissions from the specified user.
   *
   * @param \Domain\User\User $user
   * @param array $permissions
   * @return \Domain\User\User
   */
  public function removePermissions(User $user, array $permissions = [])
  {
    return $this->userRepository->removePermissions($user, $permissions);
  }

  /**
   * Set all of the permissions of the specified user.
   *
   * @param \Domain\User\User $user
   * @param array $permissions
   * @return \Domain\User\User
   */
  public function setPermissions(User $user, array $permissions = [])
  {
    return $this->userRepository->setPermissions($user, $permissions);
  }

  /**
   * Retrieve all of the permissions for the specified user.
   *
   * @param \Domain\User\User $user
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Permission\Permission>
   */
  function getPermissions(User $user)
  {
    return $this->userRepository->getPermissions($user);
  }

  /**
   * Retrieve all of the users that are associated with the specified role.
   *
   * @param \Domain\Role\Role $role
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\User\User>
   */
  public function getUsersWithRole(Role $role)
  {
    return $this->userRepository->getUsersWithRole($role);
  }

  /**
   * Retrieve all of the users that are associated with any of the specified roles.
   *
   * @param array $roles
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\User\User>
   */
  public function getUsersWithRoles(array $roles = [])
  {
    return $this->userRepository->getUsersWithRoles($roles);
  }

  /**
   * Retrieve all of the users that have access to the specified permission.
   *
   * @param \Domain\Permission\Permission $permission
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\User\User>
   */
  function getUsersWithPermission(Permission $permission)
  {
    return $this->userRepository->getUsersWithPermission($permission);
  }

  /**
   * Retrieve all of the users that have access to any of the specified permissions.
   *
   * @param array $permissions
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\User\User>
   */
  function getUsersWithPermissions(array $permissions = [])
  {
    return $this->userRepository->getUsersWithPermissions($permissions);
  }
}