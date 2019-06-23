<?php

namespace App\Contracts\Repositories;

use App\Entities\Permission;
use App\Entities\Role;
use App\Entities\User;

interface UserRepository
{
  /**
   * Retrieve all of the users.
   *
   * @return \Illuminate\Database\Eloquent\Collection<\App\Entities\User>
   */
  function all();

  /**
   * Retrieve all of the users.
   *
   * @param integer $limit
   * @param integer $offset
   * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<\App\Entities\User>
   *
   * @throws \App\Exceptions\Pagination\InvalidPaginationException
   */
  function allAsPaginated($limit = null, $offset = 1);

  /**
   * Create a new user.
   *
   * @param array $attributes
   * @return \App\Entities\User
   *
   * @throws \App\Exceptions\User\CannotCreateUserException
   */
  function create($attributes);

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
  function firstOrCreate($parameter, $search, $regex = true, $attributes = []);

  /**
   * Find a user by an unknown parameter.
   *
   * @param number|string $parameter
   * @param number|string $search
   * @param boolean $regex
   * @return \Illuminate\Database\Eloquent\Collection<\App\Entities\User>
   */
  function find($parameter, $search, $regex = true);

  /**
   * Find a user by an unknown parameter.
   *
   * @param number|string $parameter
   * @param number|string $search
   * @param boolean $regex
   * @param integer $limit
   * @param integer $offset
   * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<\App\Entities\User>
   *
   * @throws \App\Exceptions\Pagination\InvalidPaginationException
   */
  function findAsPaginated($parameter, $search, $regex = true, $limit = null, $offset = 1);

  /**
   * Find a user by identifier.
   *
   * @param string $id
   * @return \App\Entities\User
   *
   * @throws \App\Exceptions\User\UserNotFoundException
   */
  function findById($id);

  /**
   * Find a user by email.
   *
   * @param string $email
   * @return \App\Entities\User
   *
   * @throws \App\Exceptions\User\UserNotFoundException
   */
  function findByEmail($email);

  /**
   * Update a user.
   * 
   * @param \App\Entities\User $user
   * @param array $attributes
   * @return \App\Entities\User
   * 
   * @throws \App\Exceptions\User\CannotUpdateUserException
   */
  function update(User $user, $attributes);

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
  function requestEmailChange(User $user, $email);

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
  function verifyEmail(User $user, $emailVerificationToken);

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
   */
  function decodeEmailVerificationToken($emailVerificationToken);

  /**
   * Send the email verification email.
   *
   * @param \App\Entities\User $user
   * @return void
   * 
   * @throws \App\Exceptions\User\InvalidEmailVerificationTokenException
   */
  function sendEmailVerificationToken(User $user);

  /**
   * Create's a password reset token for the specified user.
   *
   * @param \App\Entities\User $user
   * @return \App\Entities\User
   */
  function forgotPassword(User $user);

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
  function resetPassword(User $user, $password, $passwordResetToken);

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
   * @param \App\Entities\User $user
   * @return void
   */
  function sendPasswordResetToken(User $user);

  /**
   * Add a role to the user.
   *
   * @param User $user
   * @param Role $role
   * @return boolean
   */
  function addRole(User $user, Role $role);

  /**
   * Add roles to the user.
   *
   * @param User $user
   * @param array $roles
   * @return boolean
   */
  function addRoles(User $user, array $roles = []);

  /**
   * Remove a role from the user.
   *
   * @param User $user
   * @param Role $role
   * @return boolean
   */
  function removeRole(User $user, Role $role);

  /**
   * Remove roles from the user.
   *
   * @param User $user
   * @param array $roles
   * @return boolean
   */
  function removeRoles(User $user, array $roles = []);

  /**
   * Set all of the roles of the specified user.
   *
   * @param User $user
   * @param array $roles
   * @return boolean
   */
  function setRoles(User $user, array $roles = []);

  /**
   * Retrieve all of the roles for the specified user.
   *
   * @param User $user
   * @return \Illuminate\Database\Eloquent\Collection<\App\Entities\Permission>
   */
  function getRoles(User $user);

  /**
   * Add a permission to the specified user.
   *
   * @param User $user
   * @param Permission $permission
   * @return boolean
   */
  function addPermission(User $user, Permission $permission);

  /**
   * Add multiple permissions to the specified user.
   *
   * @param User $user
   * @param array $permissions
   * @return boolean
   */
  function addPermissions(User $user, array $permissions = []);

  /**
   * Remove a permission from the specified user.
   *
   * @param User $user
   * @param Permission $permission
   * @return boolean
   */
  function removePermission(User $user, Permission $permission);

  /**
   * Remove multiple permissions from the specified user.
   *
   * @param User $user
   * @param array $permissions
   * @return boolean
   */
  function removePermissions(User $user, array $permissions = []);

  /**
   * Set all of the permissions of the specified user.
   *
   * @param User $user
   * @param array $permissions
   * @return boolean
   */
  function setPermissions(User $user, array $permissions = []);

  /**
   * Retrieve all of the permissions for the specified user.
   *
   * @param User $user
   * @return \Illuminate\Database\Eloquent\Collection<\App\Entities\Permission>
   */
  function getPermissions(User $user);
}
