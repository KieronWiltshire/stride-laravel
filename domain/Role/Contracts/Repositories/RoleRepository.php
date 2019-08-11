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
   * @return Collection<\Domain\Role\Role>
   */
  function all();

  /**
   * Create a new role.
   *
   * @param array $attributes
   * @return Role
   *
   * @throws CannotCreateRoleException
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
   * @return Role
   *
   * @throws CannotCreateRoleException
   */
  function firstOrCreate($parameter, $search, $regex = true, $attributes = []);

  /**
   * Find a role by an unknown parameter.
   *
   * @param number|string $parameter
   * @param number|string|array $search
   * @param boolean $regex
   * @return Collection<\Domain\Role\Role>
   */
  function find($parameter, $search, $regex = true);

  /**
   * Find a role by identifier.
   *
   * @param string $id
   * @return Role
   *
   * @throws RoleNotFoundException
   */
  function findById($id);

  /**
   * Find a role by name.
   *
   * @param string $name
   * @return Role
   *
   * @throws RoleNotFoundException
   */
  function findByName($name);

  /**
   * Retrieve the default role.
   *
   * @return Role
   *
   * @throws RoleNotFoundException
   */
  function getDefaultRole();

  /**
   * Set the specified role as the default role.
   *
   * @param Role $role
   * @return void
   */
  function setDefaultRole(Role $role);

  /**
   * Update a role.
   *
   * @param Role $role
   * @param array $attributes
   * @return Role
   *
   * @throws CannotUpdateRoleException
   */
  function update(Role $role, $attributes);

  /**
   * Add a role to the user.
   *
   * @param User $user
   * @param Role $role
   * @param boolean $persist
   * @return User
   */
  function addRoleToUser(User $user, Role $role, $persist = true);

  /**
   * Add roles to the user.
   *
   * @param User $user
   * @param \Illuminate\Support\Collection|array $roles
   * @param boolean $persist
   * @return User
   */
  function addRolesToUser(User $user, $roles = [], $persist = true);

  /**
   * Remove a role from the user.
   *
   * @param User $user
   * @param Role $role
   * @param boolean $persist
   * @return User
   */
  function removeRoleFromUser(User $user, Role $role, $persist = true);

  /**
   * Remove roles from the user.
   *
   * @param User $user
   * @param \Illuminate\Support\Collection|array $roles
   * @param boolean $persist
   * @return User
   */
  function removeRolesFromUser(User $user, $roles = [], $persist = true);

  /**
   * Set all of the roles of the specified user.
   *
   * @param User $user
   * @param \Illuminate\Support\Collection|array $roles
   * @param boolean $persist
   * @return User
   */
  function setUserRoles(User $user, $roles = [], $persist = true);

  /**
   * Retrieve all of the roles for the specified user.
   *
   * @param User $user
   * @return Collection<\Domain\Role\Role>
   */
  function getRolesForUser(User $user);

  /**
   * Retrieve all of the users that are associated with the specified role.
   *
   * @param Role $role
   * @return Collection<\Domain\User\User>
   */
  function getUsersWithRole(Role $role);

  /**
   * Retrieve all of the users that are associated with any of the specified roles.
   *
   * @param \Illuminate\Support\Collection|array $roles
   * @return Collection<\Domain\User\User>
   */
  function getUsersWithRoles($roles = []);
}
