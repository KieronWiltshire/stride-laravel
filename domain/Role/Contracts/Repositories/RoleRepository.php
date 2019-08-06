<?php

namespace Domain\Role\Contracts\Repositories;

use Domain\User\User;
use Support\Contracts\Repositories\AppRepository;
use Domain\Role\Role;

interface RoleRepository extends AppRepository
{
  /**
   * Retrieve all of the roles.
   *
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Role\Role>
   */
  function all();

  /**
   * Create a new role.
   *
   * @param array $attributes
   * @return \Domain\Role\Role
   *
   * @throws \Domain\Role\Exceptions\CannotCreateRoleException
   */
  function create($attributes);

  /**
   * Create a role if the specified search parameters could not find one
   * with the matching criteria.
   *
   * @param number|string $parameter
   * @param number|string $search
   * @param boolean $regex
   * @param array $attributes
   * @return \Domain\Role\Role
   *
   * @throws \Domain\Role\Exceptions\CannotCreateRoleException
   */
  function firstOrCreate($parameter, $search, $regex = true, $attributes = []);

  /**
   * Find a role by an unknown parameter.
   *
   * @param number|string $parameter
   * @param number|string|array $search
   * @param boolean $regex
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Role\Role>
   */
  function find($parameter, $search, $regex = true);

  /**
   * Find a role by identifier.
   *
   * @param string $id
   * @return \Domain\Role\Role
   *
   * @throws \Domain\Role\Exceptions\RoleNotFoundException
   */
  function findById($id);

  /**
   * Find a role by name.
   *
   * @param string $name
   * @return \Domain\Role\Role
   *
   * @throws \Domain\Role\Exceptions\RoleNotFoundException
   */
  function findByName($name);

  /**
   * Retrieve the default role.
   *
   * @return \Domain\Role\Role
   *
   * @throws \Domain\Role\Exceptions\RoleNotFoundException
   */
  function getDefaultRole();

  /**
   * Set the specified role as the default role.
   *
   * @param \Domain\Role\Role $role
   * @return void
   */
  function setDefaultRole(Role $role);

  /**
   * Update a role.
   *
   * @param \Domain\Role\Role $role
   * @param array $attributes
   * @return \Domain\Role\Role
   *
   * @throws \Domain\Role\Exceptions\CannotUpdateRoleException
   */
  function update(Role $role, $attributes);

  /**
   * Add a role to the user.
   *
   * @param \Domain\User\User $user
   * @param \Domain\Role\Role $role
   * @param boolean $persist
   * @return \Domain\User\User
   */
  function addRoleToUser(User $user, Role $role, $persist = true);

  /**
   * Add roles to the user.
   *
   * @param \Domain\User\User $user
   * @param \Illuminate\Support\Collection|array $roles
   * @param boolean $persist
   * @return \Domain\User\User
   */
  function addRolesToUser(User $user, $roles = [], $persist = true);

  /**
   * Remove a role from the user.
   *
   * @param \Domain\User\User $user
   * @param \Domain\Role\Role $role
   * @param boolean $persist
   * @return \Domain\User\User
   */
  function removeRoleFromUser(User $user, Role $role, $persist = true);

  /**
   * Remove roles from the user.
   *
   * @param \Domain\User\User $user
   * @param \Illuminate\Support\Collection|array $roles
   * @param boolean $persist
   * @return \Domain\User\User
   */
  function removeRolesFromUser(User $user, $roles = [], $persist = true);

  /**
   * Set all of the roles of the specified user.
   *
   * @param \Domain\User\User $user
   * @param \Illuminate\Support\Collection|array $roles
   * @param boolean $persist
   * @return \Domain\User\User
   */
  function setUserRoles(User $user, $roles = [], $persist = true);

  /**
   * Retrieve all of the roles for the specified user.
   *
   * @param \Domain\User\User $user
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Role\Role>
   */
  function getRolesForUser(User $user);

  /**
   * Retrieve all of the users that are associated with the specified role.
   *
   * @param \Domain\Role\Role $role
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\User\User>
   */
  function getUsersWithRole(Role $role);

  /**
   * Retrieve all of the users that are associated with any of the specified roles.
   *
   * @param \Illuminate\Support\Collection|array $roles
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\User\User>
   */
  function getUsersWithRoles($roles = []);
}
