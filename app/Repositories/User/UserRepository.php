<?php

namespace App\Repositories\User;

use App\Contracts\Repositories\User\UserRepository as UserRepositoryInterface;
use App\Entities\Permission;
use App\Entities\Role;
use App\Entities\User;
use App\Exceptions\User\PasswordResetTokenExpiredException;
use App\Exceptions\User\UserNotFoundException;
use App\Repositories\AppRepository;
use App\Validators\User\UserCreateValidator;
use App\Validators\User\UserEmailValidator;
use App\Validators\User\UserPasswordValidator;
use App\Validators\User\UserUpdateValidator;
use Exception;
use App\Events\User\UserCreatedEvent;
use App\Events\User\UserUpdatedEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\User\EmailVerificationToken;
use App\Mail\User\PasswordResetToken;
use App\Exceptions\User\InvalidPasswordResetTokenException;
use App\Exceptions\User\InvalidEmailVerificationTokenException;
use App\Events\User\EmailVerificationTokenGeneratedEvent;
use App\Events\User\UserEmailVerifiedEvent;
use App\Events\User\UserPasswordResetEvent;
use App\Events\User\PasswordResetTokenGeneratedEvent;

class UserRepository extends AppRepository implements UserRepositoryInterface
{
  /**
   * @var \App\Validators\User\UserCreateValidator
   */
  protected $userCreateValidator;

  /**
   * @var \App\Validators\User\UserUpdateValidator
   */
  protected $userUpdateValidator;

  /**
   * @var \App\Validators\User\UserEmailValidator
   */
  protected $userEmailValidator;

  /**
   * @var \App\Validators\User\UserPasswordValidator
   */
  protected $userPasswordValidator;

  /**
   * Create a new user repository instance.
   *
   * @param \App\Validators\User\UserCreateValidator $userCreateValidator
   * @param \App\Validators\User\UserUpdateValidator $userUpdateValidator
   * @param \App\Validators\User\UserEmailValidator $userEmailValidator
   * @param \App\Validators\User\UserPasswordValidator $userPasswordValidator
   */
  public function __construct(
    UserCreateValidator $userCreateValidator,
    UserUpdateValidator $userUpdateValidator,
    UserEmailValidator $userEmailValidator,
    UserPasswordValidator $userPasswordValidator
  ) {
    $this->userCreateValidator = $userCreateValidator;
    $this->userUpdateValidator = $userUpdateValidator;
    $this->userEmailValidator = $userEmailValidator;
    $this->userPasswordValidator = $userPasswordValidator;
  }

  /**
   * Retrieve all of the users.
   *a
   * @return \Illuminate\Database\Eloquent\Collection<\App\Entities\User>
   */
  public function all()
  {
    return $this->execute(User::query());
  }

  /**
   * Create a new user.
   *
   * @param array $attributes
   * @return \App\Entities\User
   * 
   * @throws \App\Exceptions\User\CannotCreateUserException
   */
  public function create($attributes)
  {
    if (isset($attributes['email'])) {
      $attributes['email_verification_token'] = $this->generateEmailVerificationToken($attributes['email']);
    }

    $this->userCreateValidator->validate($attributes);

    if ($user = User::create($attributes)) {
      event(new UserCreatedEvent($user));

      return $user;
    }

    throw new Exception();
  }

  /**
   * Create a user if the specified search parameters could not find one
   * with the matching criteria.
   *
   * @param number|string $parameter
   * @param number|string $search
   * @param boolean $regex
   * @param array $attributes
   * @return \App\Entities\User
   *
   * @throws \App\Exceptions\User\CannotCreateUserException
   */
  public function firstOrCreate($parameter, $search, $regex = true, $attributes = [])
  {
    if (isset($attributes['email'])) {
      $attributes['email_verification_token'] = $this->generateEmailVerificationToken($attributes['email']);
    }

    $query = User::query();

    if ($regex) {
      $query->where($parameter, 'REGEXP', $search);
    } else {
      $query->where($parameter, $search);
    }

    $user = $this->execute($query, true);

    return ($user) ? $user : $this->create($attributes);
  }

  /**
   * Find a user by an unknown parameter.
   *
   * @param number|string $parameter
   * @param number|string $search
   * @param boolean $regex
   * @return \Illuminate\Database\Eloquent\Collection<\App\Entities\User>
   */
  public function find($parameter, $search, $regex = true)
  {
    $query = User::query();

    if ($regex) {
      $query->where($parameter, 'REGEXP', $search);
    } else {
      $query->where($parameter, $search);
    }

    return $this->execute($query);
  }

  /**
   * Find a user by identifier.
   *
   * @param string $id
   * @return \App\Entities\User
   *
   * @throws \App\Exceptions\User\UserNotFoundException
   */
  public function findById($id)
  {
    $user = $this->execute(User::where('id', $id), true);

    if (!$user) {
      throw new UserNotFoundException();
    }

    return $user;
  }

  /**
   * Find a user by email.
   *
   * @param string $email
   * @return \App\Entities\User
   *
   * @throws \App\Exceptions\User\UserNotFoundException
   */
  public function findByEmail($email)
  {
    $user = $this->execute(User::where('email', $email));

    if (!$user) {
      throw new UserNotFoundException();
    }

    return $user;
  }

  /**
   * Update a user.
   * 
   * @param \App\Entities\User $user
   * @param Array $attributes
   * @return \App\Entities\User
   * 
   * @throws \App\Exceptions\User\CannotUpdateUserException
   */
  public function update(User $user, $attributes)
  {
    $this->userUpdateValidator->validate($attributes);

    if (isset($attributes['email'])) {
      $user->email = $attributes['email'];
    }

    if (isset($attributes['password'])) {
      $user->password = $attributes['password'];
    }

    if ($user->save()) {
      event(new UserUpdatedEvent($user, $attributes));

      return $user;
    }
  }

  /**
   * Router a new email verification token be generated with
   * the user's new email address to verify.
   * 
   * @param \App\Entities\User $user
   * @param string $email
   * @return \App\Entities\User
   * 
   * @throws \App\Exceptions\User\InvalidEmailException
   */
  public function requestEmailChange(User $user, $email)
  {
    $this->userEmailValidator->validate([
      'email' => $email
    ]);

    $user->email_verification_token = $this->generateEmailVerificationToken($email);

    if ($user->save()) {
      event(new EmailVerificationTokenGeneratedEvent($user));

      return $user;
    }
  }

  /**
   * Verify the user's specified email address and set their
   * email to the new one encoded within the token.
   * 
   * @param \App\Entities\User $user
   * @param string $emailVerificationToken
   * @return \App\Entities\User
   * 
   * @throws \App\Exceptions\User\InvalidEmailException
   * @throws \App\Exceptions\User\InvalidEmailVerificationTokenException
   */
  public function verifyEmail(User $user, $emailVerificationToken)
  {
    $decodedToken = $this->decodeEmailVerificationToken($emailVerificationToken);

    if ($emailVerificationToken && $decodedToken && $user->email_verification_token == $emailVerificationToken) {
      $this->userEmailValidator->validate([
        'email' => $decodedToken->email
      ]);

      $oldEmail = $user->email;

      $user->email = $decodedToken->email;
      $user->email_verified_at = now();
      $user->email_verification_token = null;

      if ($user->save()) {
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
      'token' => str_random(32) // used to append randomness
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
   * @param App\Entities\User $user
   * @return void
   * 
   * @throws \App\Exceptions\User\InvalidEmailVerificationTokenException
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
   * @param \App\Entities\User $user
   * @return \App\Entities\User
   */
  public function forgotPassword(User $user)
  {
    $user->password_reset_token = $this->generatePasswordResetToken();

    if ($user->save()) {
      event(new PasswordResetTokenGeneratedEvent($user));

      return $user;
    }
  }

  /**
   * Reset a user's password using the password reset token.
   * 
   * @param \App\Entities\User $user
   * @param string $password
   * @param string $passwordResetToken
   * @return \App\Entities\User
   * 
   * @throws \App\Exceptions\User\InvalidPasswordException
   * @throws \App\Exceptions\User\PasswordResetTokenExpiredException
   * @throws \App\Exceptions\User\InvalidPasswordResetTokenException
   */
  public function resetPassword(User $user, $password, $passwordResetToken)
  {
    $decodedToken = $this->decodePasswordResetToken($passwordResetToken);

    if ($passwordResetToken && $decodedToken && $user->password_reset_token == $passwordResetToken) {
      if (Carbon::now()->lessThan(new Carbon($decodedToken->expiry))) {
        $this->userPasswordValidator->validate([
          'password' => $password
        ]);

        $user->password = $password;
        $user->password_reset_token = null;

        if ($user->save()) {
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
      'token' => str_random(32) // used to append randomness
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
   * @param \App\Entities\User $user
   * @return void
   */
  public function sendPasswordResetToken(User $user)
  {
    Mail::to($user->email)->send(new PasswordResetToken($user->password_reset_token));
  }

  /**
   * Add a role to the user.
   *
   * @param \App\Entities\User $user
   * @param \App\Entities\Role $role
   * @return \App\Entities\User
   */
  public function addRole(User $user, Role $role)
  {
    return $this->addRoles($user, [
      $role
    ]);
  }

  /**
   * Add roles to the user.
   *
   * @param \App\Entities\User $user
   * @param array $roles
   * @return \App\Entities\User
   */
  public function addRoles(User $user, array $roles = [])
  {
    return $user->attachRoles($roles);
  }

  /**
   * Remove a role from the user.
   *
   * @param \App\Entities\User $user
   * @param \App\Entities\Role $role
   * @return \App\Entities\User
   */
  public function removeRole(User $user, Role $role)
  {
    return $this->removeRoles($user, [
      $role
    ]);
  }

  /**
   * Remove roles from the user.
   *
   * @param \App\Entities\User $user
   * @param array $roles
   * @return \App\Entities\Role
   */
  public function removeRoles(User $user, array $roles = [])
  {
    return $user->detachRoles($roles);
  }

  /**
   * Set all of the roles of the specified user.
   *
   * @param \App\Entities\User $user
   * @param array $roles
   * @return \App\Entities\User
   */
  public function setRoles(User $user, array $roles = [])
  {
    return $user->syncRoles($roles);
  }

  /**
   * Retrieve all of the roles for the specified user.
   *
   * @param \App\Entities\User $user
   * @return \Illuminate\Database\Eloquent\Collection<\App\Entities\Role>
   */
  function getRoles(User $user)
  {
    return $user->roles;
  }

  /**
   * Add a permission to the specified user.
   *
   * @param \App\Entities\User $user
   * @param \App\Entities\Permission $permission
   * @return \App\Entities\User
   */
  public function addPermission(User $user, Permission $permission)
  {
    return $this->addPermissions($user, [
      $permission
    ]);
  }

  /**
   * Add multiple permissions to the specified user.
   *
   * @param \App\Entities\User $user
   * @param array $permissions
   * @return \App\Entities\User
   */
  public function addPermissions(User $user, array $permissions = [])
  {
    return $user->attachPermissions($permissions);
  }

  /**
   * Remove a permission from the specified user.
   *
   * @param \App\Entities\User $user
   * @param \App\Entities\Permission $permission
   * @return \App\Entities\User
   */
  public function removePermission(User $user, Permission $permission)
  {
    return $this->removePermissions($user, [
      $permission
    ]);
  }

  /**
   * Remove multiple permissions from the specified user.
   *
   * @param \App\Entities\User $user
   * @param array $permissions
   * @return \App\Entities\User
   */
  public function removePermissions(User $user, array $permissions = [])
  {
    return $user->detachPermissions($permissions);
  }

  /**
   * Set all of the permissions of the specified user.
   *
   * @param \App\Entities\User $user
   * @param array $permissions
   * @return \App\Entities\User
   */
  public function setPermissions(User $user, array $permissions = [])
  {
    return $user->syncPermissions($permissions);
  }

  /**
   * Retrieve all of the permissions for the specified user.
   *
   * @param \App\Entities\User $user
   * @return \Illuminate\Database\Eloquent\Collection<\App\Entities\Permission>
   */
  function getPermissions(User $user)
  {
    return $user->permissions;
  }

  /**
   * Retrieve all of the users that are associated with the specified role.
   *
   * @param \App\Entities\Role $role
   * @return \Illuminate\Database\Eloquent\Collection<\App\Entities\User>
   */
  public function getUsersWithRole(Role $role)
  {
    return $this->getUsersWithRoles([$role]);
  }

  /**
   * Retrieve all of the users that are associated with any of the specified roles.
   *
   * @param array $roles
   * @return \Illuminate\Database\Eloquent\Collection<\App\Entities\User>
   */
  public function getUsersWithRoles(array $roles = [])
  {
    $query = User::query();

    foreach ($roles as $index => $role) {
      if ($index <= 0) {
        $query->whereRoleIs($role->name);
      } else {
        $query->orWhereRoleIs($role->name);
      }
    }

    return $this->execute($query);
  }

  /**
   * Retrieve all of the users that have access to the specified permission.
   *
   * @param \App\Entities\Permission $permission
   * @return \Illuminate\Database\Eloquent\Collection<\App\Entities\User>
   */
  function getUsersWithPermission(Permission $permission)
  {
    return $this->getUsersWithPermissions([$permission]);
  }

  /**
   * Retrieve all of the users that have access to any of the specified permissions.
   *
   * @param array $permissions
   * @return \Illuminate\Database\Eloquent\Collection<\App\Entities\User>
   */
  function getUsersWithPermissions(array $permissions = [])
  {
    $query = User::query();

    foreach ($permissions as $index => $permission) {
      if ($index <= 0) {
        $query->wherePermissionIs($permission->name);
      } else {
        $query->orWherePermissionIs($permission->name);
      }
    }

    return $this->execute($query);
  }
}