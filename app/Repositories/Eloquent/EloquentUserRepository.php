<?php

namespace App\Repositories\Eloquent;

use Validator;
use App\Entities\User;
use App\Repositories\Contracts\UserRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
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
use App\Exceptions\User\InvalidPasswordException;
use App\Exceptions\User\InvalidEmailException;
use App\Exceptions\Pagination\InvalidPaginationException;

class EloquentUserRepository implements UserRepository
{
  /**
   * Retrieve all of the users.
   * 
   * @param integer $limit
   * @param integer $offset
   * @return Illuminate\Pagination\LengthAwarePaginator<App\Entities\User>
   * 
   * @throws App\Exceptions\Pagination\InvalidPaginationException
   */
  public function all($limit, $offset = 1)
  {
    $validator = Validator::make([
      'limit' => $limit,
      'offset' => $offset
    ], [
      'limit' => 'nullable|numeric|min:1',
      'offset' => 'nullable|numeric|min:1',
    ]);

    if ($validator->fails()) {
      throw (new InvalidPaginationException())->setContext($validator->errors()->toArray());
    }

    if ($limit) {
      return User::paginate($limit, ['*'], 'page', $offset);
    } else {
      $users = User::get();

      return new LengthAwarePaginator($users->all(), $users->count(), max($users->count(), 1), 1);
    }
  }

  /**
   * Create a new user.
   *
   * @param Array $attributes
   * @return App\Entities\User
   * 
   * @throws Illuminate\Validation\ValidationException
   */
  public function create($attributes)
  {
    if (isset($attributes['email'])) {
      $attributes[] = ['email_verification_token' => $this->generateEmailVerificationToken($attributes['email'])];
    }

    $validator = Validator::make($attributes, [
      'email' => 'required|unique:users|email',
      'password' => 'required|min:6',
    ]);

    if ($validator->fails()) {
      throw ValidationException::withMessages($validator->errors()->toArray());
    }

    if ($user = User::create($attributes)) {
      event(new UserCreatedEvent($user));

      return $user;
    }

    throw new Exception();
  }

  /**
   * Find a user by an unknown parameter.
   *
   * @param number|string $parameter
   * @param number|string $search
   * @param boolean $regex
   * @return Illuminate\Pagination\LengthAwarePaginator<App\Entities\User>
   */
  public function find($parameter, $search, $regex = true, $limit, $offset = 1)
  {
    $query = User::query();

    if ($regex) {
      $query->where($parameter, 'REGEXP', $search)->get();
    } else {
      $query->where($parameter, $search)->get();
    }

    if ($limit) {
      return $query->paginate($limit, ['*'], 'page', $offset);
    } else {
      $users = $query->get();

      return new LengthAwarePaginator($users->all(), $users->count(), max($users->count(), 1), 1);
    }
  }

  /**
   * Find a user by identifier.
   *
   * @param string $id
   * @return App\Entities\User
   */
  public function findById($id)
  {
    return User::find($id);
  }

  /**
   * Find a user by email.
   *
   * @param string $email
   * @return App\Entities\User
   */
  public function findByEmail($email)
  {
    return User::where('email', $email)->first();
  }

  /**
   * Update a user.
   * 
   * @param App\Entities\User $user
   * @param Array $attributes
   * @return App\Entities\User
   * 
   * @throws Illuminate\Validation\ValidationException
   */
  public function update($user, $attributes)
  {
    if ($user instanceof User) {
      $validator = Validator::make($attributes, [
        'email' => 'nullable|unique:users|email',
        'password' => 'nullable|min:6',
      ]);

      if ($validator->fails()) {
        throw ValidationException::withMessages($validator->errors()->toArray());
      }

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

    throw new Exception();
  }

  /**
   * Request a new email verification token be generated with
   * the user's new email address to verify.
   * 
   * @param App\Entities\User $user
   * @param string $email
   * @return App\Entities\User
   * 
   * @throws App\Exceptions\User\InvalidEmailException
   */
  public function requestEmailChange($user, $email)
  {
    if ($user instanceof User) {
      $validator = Validator::make([
        'email' => $email
      ], [
        'email' => 'unique:users|email',
      ]);

      if ($validator->fails()) {
        throw (new InvalidEmailException())->setContext($validator->errors()->toArray());
      }

      $user->email_verification_token = $this->generateEmailVerificationToken($email);

      if ($user->save()) {
        event(new EmailVerificationTokenGeneratedEvent($user));

        return $user;
      }
    }

    throw new Exception();
  }

  /**
   * Verify the user's specified email address and set their
   * email to the new one encoded within the token.
   * 
   * @param App\Entities\User $user
   * @param string $emailVerificationToken
   * @return App\Entities\User
   * 
   * @throws App\Exceptions\User\InvalidEmailException
   * @throws App\Exceptions\User\InvalidEmailVerificationTokenException
   */
  public function verifyEmail($user, $emailVerificationToken)
  {
    if ($user instanceof User) {
      $decodedToken = $this->decodeEmailVerificationToken($emailVerificationToken);

      if ($emailVerificationToken && $decodedToken && $user->email_verification_token == $emailVerificationToken) {
        $validator = Validator::make([
          'email' => $decodedToken->email
        ], [
          'email' => 'unique:users|email',
        ]);

        if ($validator->fails()) {
          throw (new InvalidEmailException())->setContext($validator->errors()->toArray());
        }

        $oldEmail = $user->email;

        $user->email = $decodedToken->email;
        $user->email_verified = true;
        $user->email_verification_token = null;

        if ($user->save()) {
          event(new UserEmailVerifiedEvent($user, $oldEmail));

          return $user;
        }
      } else {
        throw new InvalidEmailVerificationTokenException();
      }
    }

    throw new Exception();
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
   * @throws App\Exceptions\User\InvalidEmailVerificationTokenException
   */
  public function sendEmailVerificationToken($user)
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
   * @param App\Entities\User $user
   * @return App\Entities\User
   */
  public function forgotPassword($user)
  {
    if ($user instanceof User) {
      $user->password_reset_token = $this->generatePasswordResetToken();

      if ($user->save()) {
        event(new PasswordResetTokenGeneratedEvent($user));

        return $user;
      }
    }

    throw new Exception();
  }

  /**
   * Reset a user's password using the password reset token.
   * 
   * @param App\Entities\User $user
   * @param string $password
   * @param string $passwordResetToken
   * @return App\Entities\User
   * 
   * @throws App\Exceptions\User\InvalidPasswordException
   * @throws App\Exceptions\User\PasswordResetTokenExpiredException
   * @throws App\Exceptions\User\InvalidPasswordResetTokenException
   */
  public function resetPassword($user, $password, $passwordResetToken)
  {
    if ($user instanceof User) {
      $decodedToken = $this->decodePasswordResetToken($passwordResetToken);

      if ($passwordResetToken && $decodedToken && $user->password_reset_token == $passwordResetToken) {
        if (Carbon::now()->lessThan(new Carbon($decodedToken->expiry))) {
          $validator = Validator::make([
            'password' => $password
          ], [
            'password' => 'nullable|min:6',
          ]);

          if ($validator->fails()) {
            throw (new InvalidPasswordException())->setContext($validator->errors()->toArray());
          }

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

    throw new Exception();
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
   * @param App\Entities\User $user
   * @return void
   */
  public function sendPasswordResetToken($user)
  {
    Mail::to($user->email)->send(new PasswordResetToken($user->password_reset_token));
  }
}
