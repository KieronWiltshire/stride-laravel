<?php

namespace App\Repositories\Contracts;

interface UserRepository
{
  /**
   * Retrieve all of the users.
   * 
   * @param integer $limit
   * @param integer $offset
   * @return Illuminate\Pagination\LengthAwarePaginator<App\Entities\User>
   */
  function all($limit, $offset = 1);

  /**
   * Create a new user.
   *
   * @param Array $input
   * @return App\Entities\User
   * @throws Illuminate\Validation\ValidationException
   */
  function create($input);

  /**
   * Find a user by an unknown parameter.
   *
   * @param number|string $parameter
   * @param number|string $search
   * @param boolean $regex
   * @return Illuminate\Pagination\LengthAwarePaginator<App\Entities\User>
   */
  function find($parameter, $search, $regex = true, $limit, $offset = 1);

  /**
   * Find a user by identifier.
   *
   * @param string $id
   * @return App\Entities\User
   */
  function findById($id);

  /**
   * Find a user by email.
   *
   * @param string $email
   * @return App\Entities\User
   */
  function findByEmail($email);

  /**
   * Edit a user.
   * 
   * @param App\Entities\User $user
   * @param Array $input
   * @return boolean
   * @throws Illuminate\Validation\ValidationException
   */
  public function edit($user, $input);

  /**
   * Generate an email verification token for the specified email address.
   *
   * @param string $email
   * @return string
   */
  function generateEmailVerificationToken($email);

  /**
   * Parse an email verification token.
   *
   * @param string $emailVerificationToken
   * @return Object
   */
  function parseEmailVerificationToken($emailVerificationToken);

  /**
   * Send the email verification email.
   *
   * @param App\Entities\User $user
   * @return boolean
   */
  function sendEmailVerificationToken($user);

  /**
   * Change a user's password.
   *
   * @param App\Entities\User $user
   * @param string $password
   * @return App\Entities\User
   */
  function changePassword($user, $password);

  /**
   * Create's a password reset token for the specified user.
   *
   * @param App\Entities\User $user
   * @return App\Entities\User
   */
  function forgotPassword($user);

  /**
   * Reset the user's password using the password reset token.
   *
   * @param App\Entities\User $user
   * @param string $newPassword
   * @param string $passwordResetToken
   * @return App\Entities\User
   */
  function resetPassword($user, $newPassword, $passwordResetToken);

  /**
   * Generate a password reset token.
   *
   * @return string
   */
  function generatePasswordResetToken();

  /**
   * Parse a password reset token.
   *
   * @param string $passwordResetToken
   * @return Object
   */
  function parsePasswordResetToken($passwordResetToken);

  /**
   * Send the user a password reset email.
   *
   * @param App\Entities\User $user
   * @return void
   */
  function sendPasswordResetToken($user);
}
