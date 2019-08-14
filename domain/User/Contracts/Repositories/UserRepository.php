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
     * @return Collection<User>
     */
    public function all();

    /**
     * Create a new user.
     *
     * @param array $attributes
     * @return User
     *
     * @throws CannotCreateUserException
     */
    public function create($attributes);

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
    public function firstOrCreate($parameter, $search, $regex = true, $attributes = []);

    /**
     * Find a user by an unknown parameter.
     *
     * @param number|string $parameter
     * @param number|string|array $search
     * @param boolean $regex
     * @return Collection<User>
     */
    public function find($parameter, $search, $regex = true);

    /**
     * Find a user by identifier.
     *
     * @param string $id
     * @return User
     *
     * @throws UserNotFoundException
     */
    public function findById($id);

    /**
     * Find a user by email.
     *
     * @param string $email
     * @return User
     *
     * @throws UserNotFoundException
     */
    public function findByEmail($email);

    /**
     * Update a user.
     *
     * @param User $user
     * @param array $attributes
     * @return User
     *
     * @throws CannotUpdateUserException
     */
    public function update(User $user, $attributes);
}
