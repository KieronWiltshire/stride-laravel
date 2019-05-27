<?php

namespace App\Repositories\Eloquent;

use Validator;
use App\Entities\User;
use App\Repositories\Contracts\UserRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Exception;
use App\Events\User\UserCreatedEvent;
use App\Exceptions\HttpError\BadRequestError;

class EloquentUserRepository implements UserRepository
{
  /**
   * Retrieve all of the users.
   * 
   * @param integer $limit
   * @param integer $offset
   * @return Illuminate\Pagination\LengthAwarePaginator<App\Entities\User>
   */
  public function all($limit, $offset = 1)
  {
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
   * @param Array $input
   * @return App\Entities\User
   * @throws Illuminate\Validation\ValidationException
   */
  public function create($input)
  {
    if (isset($input['email'])) {
      $input[] = ['email_verification_token' => $this->generateEmailVerificationToken($input['email'])];
    }

    $validator = Validator::make($input, [
      'email' => 'required|unique:users|email',
      'password' => 'required',
    ]);

    if ($validator->fails()) {
      throw ValidationException::withMessages($validator->errors()->toArray());
    }

    if ($user = User::create($input)) {
      event(new UserCreatedEvent($user));

      return $user;
    } else {
      throw new Exception();
    }
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
   * Edit a user.
   * 
   * @param App\Entities\User $user
   * @param Array $input
   * @return boolean
   * @throws Illuminate\Validation\ValidationException
   */
  public function edit($user, $input)
  { }

  /**
   * Generate an email verification token for the specified email address.
   *
   * @param string $email
   * @return string
   */
  public function generateEmailVerificationToken($email)
  { }

  /**
   * Parse an email verification token.
   *
   * @param string $emailVerificationToken
   * @return Object
   */
  public function parseEmailVerificationToken($emailVerificationToken)
  { }

  /**
   * Send the email verification email.
   *
   * @param App\Entities\User $user
   * @return boolean
   */
  public function sendEmailVerificationToken($user)
  { }

  /**
   * Create's a password reset token for the specified user.
   *
   * @param App\Entities\User $user
   * @return App\Entities\User
   */
  public function forgotPassword($user)
  { }

  /**
   * Reset the user's password using the password reset token.
   *
   * @param App\Entities\User $user
   * @param string $newPassword
   * @param string $passwordResetToken
   * @return App\Entities\User
   */
  public function resetPassword($user, $newPassword, $passwordResetToken)
  { }

  /**
   * Generate a password reset token.
   *
   * @return string
   */
  public function generatePasswordResetToken()
  { }

  /**
   * Parse a password reset token.
   *
   * @param string $passwordResetToken
   * @return Object
   */
  public function parsePasswordResetToken($passwordResetToken)
  { }

  /**
   * Send the user a password reset email.
   *
   * @param App\Entities\User $user
   * @return void
   */
  public function sendPasswordResetToken($user)
  { }
}
