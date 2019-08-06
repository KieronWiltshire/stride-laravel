<?php

namespace Domain\Permission\Contracts\Repositories;

use Domain\Role\Role;
use Domain\User\User;
use Support\Contracts\Repositories\AppRepository;
use Domain\Permission\Permission;

interface PermissionRepository extends AppRepository
{

  /**
   * Retrieve all of the permissions.
   *
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Permission\Permission>
   */
  function all();

  /**
   * Create a new permission.
   *
   * @param array $attributes
   * @return \Domain\Permission\Permission
   *
   * @throws \Domain\Permission\Exceptions\CannotCreatePermissionException
   */
  function create($attributes);

  /**
   * Create a permission if the specified search parameters could not find one
   * with the matching criteria.
   *
   * @param number|string $parameter
   * @param number|string $search
   * @param boolean $regex
   * @param array $attributes
   * @return \Domain\Permission\Permission
   *
   * @throws \Domain\Permission\Exceptions\CannotCreatePermissionException
   */
  function firstOrCreate($parameter, $search, $regex = true, $attributes = []);

  /**
   * Find a permission by an unknown parameter.
   *
   * @param number|string $parameter
   * @param number|string|array $search
   * @param boolean $regex
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Permission\Permission>
   */
  function find($parameter, $search, $regex = true);

  /**
   * Find a permission by identifier.
   *
   * @param string $id
   * @return \Domain\Permission\Permission
   *
   * @throws \Domain\Permission\Exceptions\PermissionNotFoundException
   */
  function findById($id);

  /**
   * Find a permission by name.
   *
   * @param string $name
   * @return \Domain\Permission\Permission
   *
   * @throws \Domain\Permission\Exceptions\PermissionNotFoundException
   */
  function findByName($name);

  /**
   * Update a permission.
   *
   * @param \Domain\Permission\Permission $permission
   * @param array $attributes
   * @return \Domain\Permission\Permission
   *
   * @throws \Domain\Permission\Exceptions\CannotUpdatePermissionException
   */
  function update(Permission $permission, $attributes);

  /**
   * Add a permission to the specified role.
   *
   * @param \Domain\Role\Role $role
   * @param \Domain\Permission\Permission $permission
   * @return \Domain\Role\Role
   */
  function addPermissionToRole(Role $role, Permission $permission);

  /**
   * Add a permission to the specified user.
   *
   * @param \Domain\User\User $user
   * @param \Domain\Permission\Permission $permission
   * @return \Domain\User\User
   */
  function addPermissionToUser(User $user, Permission $permission);

  /**
   * Add multiple permissions to the specified role.
   *
   * @param \Domain\Role\Role $role
   * @param \Illuminate\Support\Collection|array $permissions
   * @return \Domain\Role\Role
   */
  function addPermissionsToRole(Role $role, $permissions = []);

  /**
   * Add multiple permissions to the specified user.
   *
   * @param \Domain\User\User $user
   * @param \Illuminate\Support\Collection|array $permissions
   * @return \Domain\User\User
   */
  function addPermissionsToUser(User $user, $permissions = []);

  /**
   * Remove a permission from the specified role.
   *
   * @param \Domain\Role\Role $role
   * @param \Domain\Permission\Permission $permission
   * @return \Domain\Role\Role
   */
  function removePermissionFromRole(Role $role, Permission $permission);

  /**
   * Remove a permission from the specified user.
   *
   * @param \Domain\User\User $user
   * @param \Domain\Permission\Permission $permission
   * @return \Domain\User\User
   */
  function removePermissionFromUser(User $user, Permission $permission);

  /**
   * Remove multiple permissions from the specified role.
   *
   * @param \Domain\Role\Role $role
   * @param \Illuminate\Support\Collection|array $permissions
   * @return \Domain\Role\Role
   */
  function removePermissionsFromRole(Role $role, $permissions = []);

  /**
   * Remove multiple permissions from the specified user.
   *
   * @param \Domain\User\User $user
   * @param \Illuminate\Support\Collection|array $permissions
   * @return \Domain\User\User
   */
  function removePermissionsFromUser(User $user, $permissions = []);

  /**
   * Set all of the permissions of the specified role.
   *
   * @param \Domain\Role\Role $role
   * @param \Illuminate\Support\Collection|array $permissions
   * @return \Domain\Role\Role
   */
  function setRolePermissions(Role $role, $permissions = []);

  /**
   * Set all of the permissions of the specified user.
   *
   * @param \Domain\User\User $user
   * @param \Illuminate\Support\Collection|array $permissions
   * @return \Domain\User\User
   */
  function setUserPermissions(User $user, $permissions = []);

  /**
   * Retrieve all of the permissions for the specified role.
   *
   * @param \Domain\Role\Role $role
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Permission\Permission>
   */
  function getPermissionsForRole(Role $role);

  /**
   * Retrieve all of the permissions for the specified user.
   *
   * @param \Domain\User\User $user
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Permission\Permission>
   */
  function getPermissionsForUser(User $user);

  /**
   * Retrieve all of the roles that have access to the specified permission.
   *
   * @param \Domain\Permission\Permission $permission
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Role\Role>
   */
  function getRolesWithPermission(Permission $permission);

  /**
   * Retrieve all of the users that have access to the specified permission.
   *
   * @param \Domain\Permission\Permission $permission
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\User\User>
   */
  function getUsersWithPermission(Permission $permission);

  /**
   * Retrieve all of the roles that have access to any of the specified permissions.
   *
   * @param \Illuminate\Support\Collection|array $permissions
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Role\Role>
   */
  function getRolesWithPermissions($permissions = []);

  /**
   * Retrieve all of the users that have access to any of the specified permissions.
   *
   * @param \Illuminate\Support\Collection|array $permissions
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\User\User>
   */
  function getUsersWithPermissions($permissions = []);
}
