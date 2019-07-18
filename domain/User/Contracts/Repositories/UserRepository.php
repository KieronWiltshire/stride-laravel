<?php

namespace Domain\User\Contracts\Repositories;

use Domain\Permission\Permission;
use Domain\Role\Role;
use Domain\User\User;
use Infrastructure\Contracts\Repositories\AppRepository;

interface UserRepository extends AppRepository
{
  /**
   * Retrieve all of the users.
   *
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\User\User>
   */
  function all();

  /**
   * Create a new user.
   *
   * @param array $attributes
   * @return \Domain\User\User
   *
   * @throws \Domain\User\Exceptions\CannotCreateUserException
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
   * @return \Domain\User\User
   *
   * @throws \Domain\User\Exceptions\CannotCreateUserException
   */
  function firstOrCreate($parameter, $search, $regex = true, $attributes = []);

  /**
   * Find a user by an unknown parameter.
   *
   * @param number|string $parameter
   * @param number|string|array $search
   * @param boolean $regex
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\User\User>
   */
  function find($parameter, $search, $regex = true);

  /**
   * Find a user by identifier.
   *
   * @param string $id
   * @return \Domain\User\User
   *
   * @throws \Domain\User\Exceptions\UserNotFoundException
   */
  function findById($id);

  /**
   * Find a user by email.
   *
   * @param string $email
   * @return \Domain\User\User
   *
   * @throws \Domain\User\Exceptions\UserNotFoundException
   */
  function findByEmail($email);

  /**
   * Update a user.
   *
   * @param \Domain\User\User $user
   * @param array $attributes
   * @return \Domain\User\User
   *
   * @throws \Domain\User\Exceptions\CannotUpdateUserException
   */
  function update(User $user, $attributes);
}
