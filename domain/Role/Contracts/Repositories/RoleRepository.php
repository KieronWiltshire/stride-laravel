<?php

namespace Domain\Role\Contracts\Repositories;

use Domain\Permission\Permission;
use Infrastructure\Contracts\Repositories\AppRepository;
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
   * @param number|string $search
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
   * Add a permission to the specified role.
   *
   * @param \Domain\Role\Role $role
   * @param \Domain\Permission\Permission $permission
   * @return \Domain\Role\Role
   */
  function addPermission(Role $role, Permission $permission);

  /**
   * Add multiple permissions to the specified role.
   *
   * @param \Domain\Role\Role $role
   * @param array $permissions
   * @return \Domain\Role\Role
   */
  function addPermissions(Role $role, array $permissions = []);

  /**
   * Remove a permission from the specified role.
   *
   * @param \Domain\Role\Role $role
   * @param \Domain\Permission\Permission $permission
   * @return \Domain\Role\Role
   */
  function removePermission(Role $role, Permission $permission);

  /**
   * Remove multiple permissions from the specified role.
   *
   * @param \Domain\Role\Role $role
   * @param array $permissions
   * @return \Domain\Role\Role
   */
  function removePermissions(Role $role, array $permissions = []);

  /**
   * Set all of the permissions of the specified role.
   *
   * @param \Domain\Role\Role $role
   * @param array $permissions
   * @return \Domain\Role\Role
   */
  function setPermissions(Role $role, array $permissions = []);

  /**
   * Retrieve all of the permissions for the specified role.
   *
   * @param \Domain\Role\Role $role
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Permission\Permission>
   */
  function getPermissions(Role $role);

  /**
   * Retrieve all of the roles that have access to the specified permission.
   *
   * @param \Domain\Permission\Permission $permission
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Role\Role>
   */
  function getRolesWithPermission(Permission $permission);

  /**
   * Retrieve all of the roles that have access to any of the specified permissions.
   *
   * @param array $permissions
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Role\Role>
   */
  function getRolesWithPermissions(array $permissions = []);
}
