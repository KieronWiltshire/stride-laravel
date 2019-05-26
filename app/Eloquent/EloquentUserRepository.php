<?php

namespace App\Eloquent;

use App\Contracts\UserRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentUserRepository implements UserRepository
{
  /**
   * Retrieve all of the users.
   * 
   * @param integer $limit
   * @param integer $offset
   * @return LengthAwarePaginator<App\Entities\User>
   */
  public function all($limit, $offset)
  {
    if ($limit) {
      return User::paginate($limit, ['*'], 'page', $offset);
    } else {
      $users = User::get();

      return new LengthAwarePaginator($users->all(), $users->count(), $users->count(), 1);
    }
  }

  /**
   * Create a new user.
   *
   * @param string email
   * @param string username
   * @param string password
   * @return App\Entities\User
   */
  public function create($email, $password)
  { }

  /**
   * Find a user by an unknown parameter.
   *
   * @param number|string $param
   * @param number|string $value
   * @param boolean $regex
   * @return App\Entities\User
   */
  public function find($param, $value, $regex = true)
  { }

  /**
   * Find a user by identifier.
   *
   * @param string $id
   * @return App\Entities\User
   */
  public function findById($id)
  { }

  /**
   * Find a user by email.
   *
   * @param string $email
   * @return App\Entities\User
   */
  public function findByEmail($email)
  { }

  /**
   * Change the email of the specified user.
   *
   * @param App\Entities\User $user
   * @param string $email
   * @param boolean $requireVerification
   * @return App\Entities\User
   */
  public function changeEmail($user, $email, $requireVerification = true)
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
   * Change a user's password.
   *
   * @param App\Entities\User $user
   * @param string $password
   * @return App\Entities\User
   */
  public function changePassword($user, $password)
  { }

  /**
   * Compare a user's password.
   *
   * @param App\Entities\User $user
   * @param string $password
   * @return boolean
   */
  public function comparePassword($user, $password)
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
