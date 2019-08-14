<?php

namespace Domain\Role\Contracts\Repositories;

use Domain\Role\Exceptions\CannotCreateRoleException;
use Domain\Role\Exceptions\CannotUpdateRoleException;
use Domain\Role\Exceptions\RoleNotFoundException;
use Domain\User\User;
use Illuminate\Database\Eloquent\Collection;
use Support\Contracts\Repositories\AppRepository;
use Domain\Role\Role;

interface RoleRepository extends AppRepository
{
    /**
     * Retrieve all of the roles.
     *
     * @return Collection
     */
    public function all();

    /**
     * Create a new role.
     *
     * @param array $attributes
     * @return Role
     *
     * @throws CannotCreateRoleException
     */
    public function create($attributes);

    /**
     * Create a role if the specified search parameters could not find one
     * with the matching criteria.
     *
     * @param number|string $parameter
     * @param number|string $search
     * @param boolean $regex
     * @param array $attributes
     * @return Role
     *
     * @throws CannotCreateRoleException
     */
    public function firstOrCreate($parameter, $search, $regex = true, $attributes = []);

    /**
     * Find a role by an unknown parameter.
     *
     * @param number|string $parameter
     * @param number|string|array $search
     * @param boolean $regex
     * @return Collection
     */
    public function find($parameter, $search, $regex = true);

    /**
     * Find a role by identifier.
     *
     * @param string $id
     * @return Role
     *
     * @throws RoleNotFoundException
     */
    public function findById($id);

    /**
     * Find a role by name.
     *
     * @param string $name
     * @return Role
     *
     * @throws RoleNotFoundException
     */
    public function findByName($name);

    /**
     * Retrieve the default role.
     *
     * @return Role
     *
     * @throws RoleNotFoundException
     */
    public function getDefaultRole();

    /**
     * Set the specified role as the default role.
     *
     * @param Role $role
     * @return void
     */
    public function setDefaultRole(Role $role);

    /**
     * Update a role.
     *
     * @param Role $role
     * @param array $attributes
     * @return Role
     *
     * @throws CannotUpdateRoleException
     */
    public function update(Role $role, $attributes);

    /**
     * Add a role to the user.
     *
     * @param User $user
     * @param Role $role
     * @param boolean $persist
     * @return User
     */
    public function addRoleToUser(User $user, Role $role, $persist = true);

    /**
     * Add roles to the user.
     *
     * @param User $user
     * @param Collection|array $roles
     * @param boolean $persist
     * @return User
     */
    public function addRolesToUser(User $user, $roles = [], $persist = true);

    /**
     * Remove a role from the user.
     *
     * @param User $user
     * @param Role $role
     * @param boolean $persist
     * @return User
     */
    public function removeRoleFromUser(User $user, Role $role, $persist = true);

    /**
     * Remove roles from the user.
     *
     * @param User $user
     * @param Collection|array $roles
     * @param boolean $persist
     * @return User
     */
    public function removeRolesFromUser(User $user, $roles = [], $persist = true);

    /**
     * Set all of the roles of the specified user.
     *
     * @param User $user
     * @param Collection|array $roles
     * @param boolean $persist
     * @return User
     */
    public function setUserRoles(User $user, $roles = [], $persist = true);

    /**
     * Retrieve all of the roles for the specified user.
     *
     * @param User $user
     * @return Collection
     */
    public function getRolesForUser(User $user);

    /**
     * Retrieve all of the users that are associated with the specified role.
     *
     * @param Role $role
     * @return Collection
     */
    public function getUsersWithRole(Role $role);

    /**
     * Retrieve all of the users that are associated with any of the specified roles.
     *
     * @param Collection|array $roles
     * @return Collection
     */
    public function getUsersWithRoles($roles = []);
}
