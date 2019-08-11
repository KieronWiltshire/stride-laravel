<?php

namespace Domain\User\Contracts\Repositories;

use Domain\User\Exceptions\CannotCreateUserException;
use Domain\User\Exceptions\CannotUpdateUserException;
use Domain\User\Exceptions\UserNotFoundException;
use Domain\User\User;
use Illuminate\Database\Eloquent\Collection;
use Support\Contracts\Repositories\AppRepository;

interface UserRepository extends AppRepository
{
  /**
   * Retrieve all of the users.
   *
   * @return Collection<\Domain\User\User>
   */
  function all();

  /**
   * Create a new user.
   *
   * @param array $attributes
   * @return User
   *
   * @throws CannotCreateUserException
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
   * @return User
   *
   * @throws CannotCreateUserException
   */
  function firstOrCreate($parameter, $search, $regex = true, $attributes = []);

  /**
   * Find a user by an unknown parameter.
   *
   * @param number|string $parameter
   * @param number|string|array $search
   * @param boolean $regex
   * @return Collection<\Domain\User\User>
   */
  function find($parameter, $search, $regex = true);

  /**
   * Find a user by identifier.
   *
   * @param string $id
   * @return User
   *
   * @throws UserNotFoundException
   */
  function findById($id);

  /**
   * Find a user by email.
   *
   * @param string $email
   * @return User
   *
   * @throws UserNotFoundException
   */
  function findByEmail($email);

  /**
   * Update a user.
   *
   * @param User $user
   * @param array $attributes
   * @return User
   *
   * @throws CannotUpdateUserException
   */
  function update(User $user, $attributes);
}
