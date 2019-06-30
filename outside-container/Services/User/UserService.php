<?php

namespace App\Services\User;

use App\Entities\User;
use App\Events\User\EmailVerificationTokenGeneratedEvent;
use App\Contracts\Repositories\User\UserRepository;
use App\Events\User\PasswordResetTokenGeneratedEvent;
use App\Events\User\UserEmailVerifiedEvent;
use App\Events\User\UserPasswordResetEvent;
use App\Exceptions\User\InvalidEmailVerificationTokenException;
use App\Exceptions\User\InvalidPasswordResetTokenException;
use App\Exceptions\User\PasswordResetTokenExpiredException;
use App\Mail\User\EmailVerificationToken;
use App\Mail\User\PasswordResetToken;
use App\Validators\User\UserEmailValidator;
use App\Validators\User\UserPasswordValidator;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserService
{
  /**
   * @var \App\Contracts\Repositories\User\UserRepository
   */
  protected $userRepository;

  /**
   * @var \App\Validators\User\UserEmailValidator
   */
  protected $userEmailValidator;

  /**
   * @var \App\Validators\User\UserPasswordValidator
   */
  protected $userPasswordValidator;

  /**
   * Create a new user service instance.
   *
   * @param \App\Contracts\Repositories\User\UserRepository $userRepository
   * @param \App\Validators\User\UserEmailValidator $userEmailValidator
   * @param \App\Validators\User\UserPasswordValidator $userPasswordValidator
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
   * @return \Illuminate\Database\Eloquent\Collection<\App\Entities\User>
   */
  public function all()
  {
    return $this->userRepository->all();
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
   * @return \App\Entities\User
   *
   * @throws \App\Exceptions\User\CannotCreateUserException
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
   * @return \Illuminate\Database\Eloquent\Collection<\App\Entities\User>
   */
  public function find($parameter, $search, $regex = true)
  {
    return $this->userRepository->find($parameter, $search, $regex);
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
    return $this->userRepository->findById($id);
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
    return $this->userRepository->findByEmail($email);
  }

  /**
   * Update a user.
   *
   * @param \App\Entities\User $user
   * @param array $attributes
   * @return \App\Entities\User
   *
   * @throws \App\Exceptions\User\CannotUpdateUserException
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
   * @return \Illuminate\Pagination\LengthAwarePaginator<\App\Entities\User>
   *
   * @throws \App\Exceptions\Pagination\InvalidPaginationException
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
   * @return \Illuminate\Pagination\LengthAwarePaginator<\App\Entities\User>
   */
  function search($parameter, $search, $regex = true, $limit = null, $offset = 1)
  {
    return $this->userRepository->paginate($limit, $offset)->find($parameter, $search, $regex);
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
   * @param \App\Entities\User $user
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
   * @param \App\Entities\User $user
   * @return void
   */
  public function sendPasswordResetToken(User $user)
  {
    if ($user->password_reset_token) {
      Mail::to($user->email)->send(new PasswordResetToken($user->password_reset_token));
    }
  }
}