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
   * @param Array $attributes
   * @return App\Entities\User
   * 
   * @throws Illuminate\Validation\ValidationException
   */
  function create($attributes);

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
   * Update a user.
   * 
   * @param App\Entities\User $user
   * @param Array $attributes
   * @return App\Entities\User
   * 
   * @throws Illuminate\Validation\ValidationException
   */
  function update($user, $attributes);

  /**
   * Request a new email verification token be generated with
   * the user's new email address to verify.
   * 
   * @param App\Entities\User $user
   * @param string $email
   * @return App\Entities\User
   * 
   * @throws Illuminate\Validation\ValidationException
   */
  function requestEmailChange($user, $email);

  /**
   * Verify the user's specified email address and set their
   * email to the new one encoded within the token.
   * 
   * @param App\Entities\User $user
   * @param string $emailVerificationToken
   * @return App\Entities\User
   * 
   * @throws Illuminate\Validation\ValidationException
   * @throws App\Exceptions\User\InvalidEmailVerificationTokenException
   */
  function verifyEmail($user, $emailVerificationToken);

  /**
   * Generate an email verification token for the specified email address.
   *
   * @param string $email
   * @return string
   */
  function generateEmailVerificationToken($email);

  /**
   * Decode an email verification token.
   *
   * @param string $emailVerificationToken
   * @return Object
   * 
   * @throws Illuminate\Validation\ValidationException
   */
  function decodeEmailVerificationToken($emailVerificationToken);

  /**
   * Send the email verification email.
   *
   * @param App\Entities\User $user
   * @return void
   * 
   * @throws App\Exceptions\User\InvalidEmailVerificationTokenException
   */
  function sendEmailVerificationToken($user);

  /**
   * Create's a password reset token for the specified user.
   *
   * @param App\Entities\User $user
   * @return App\Entities\User
   */
  function forgotPassword($user);

  /**
   * Reset a user's password using the password reset token.
   * 
   * @param App\Entities\User $user
   * @param string $password
   * @param string $passwordResetToken
   * @return App\Entities\User
   * 
   * @throws Illuminate\Validation\ValidationException
   * @throws App\Exceptions\User\PasswordResetTokenExpiredException
   * @throws App\Exceptions\User\InvalidPasswordResetTokenException
   */
  function resetPassword($user, $password, $passwordResetToken);

  /**
   * Generate a password reset token.
   *
   * @return string
   */
  function generatePasswordResetToken();

  /**
   * Decode a password reset token.
   *
   * @param string $passwordResetToken
   * @return Object
   */
  function decodePasswordResetToken($passwordResetToken);

  /**
   * Send the user a password reset email.
   *
   * @param App\Entities\User $user
   * @return void
   */
  function sendPasswordResetToken($user);
}
