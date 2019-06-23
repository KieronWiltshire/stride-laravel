<?php

namespace App\Contracts\Repositories;

use App\Entities\Permission;
use App\Entities\Role;

interface RoleRepository
{
  /**
   * Retrieve all of the roles.
   *
   * @return \Illuminate\Database\Eloquent\Collection<\App\Entities\Role>
   */
  function all();

  /**
   * Retrieve all of the roles.
   *
   * @param integer $limit
   * @param integer $offset
   * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<\App\Entities\Role>
   *
   * @throws \App\Exceptions\Pagination\InvalidPaginationException
   */
  function allAsPaginated($limit = null, $offset = 1);

  /**
   * Create a new role.
   *
   * @param array $attributes
   * @return \App\Entities\Role
   *
   * @throws \App\Exceptions\Role\CannotCreateRoleException
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
   * @return \App\Entities\Role
   *
   * @throws \App\Exceptions\Role\CannotCreateRoleException
   */
  function firstOrCreate($parameter, $search, $regex = true, $attributes = []);

  /**
   * Find a role by an unknown parameter.
   *
   * @param number|string $parameter
   * @param number|string $search
   * @param boolean $regex
   * @return \Illuminate\Database\Eloquent\Collection<\App\Entities\Role>
   */
  function find($parameter, $search, $regex = true);

  /**
   * Find a role by an unknown parameter.
   *
   * @param number|string $parameter
   * @param number|string $search
   * @param boolean $regex
   * @param integer $limit
   * @param integer $offset
   * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<\App\Entities\Role>
   *
   * @throws \App\Exceptions\Pagination\InvalidPaginationException
   */
  function findAsPaginated($parameter, $search, $regex = true, $limit = null, $offset = 1);

  /**
   * Find a role by identifier.
   *
   * @param string $id
   * @return \App\Entities\Role
   *
   * @throws \App\Exceptions\Role\RoleNotFoundException
   */
  function findById($id);

  /**
   * Find a role by name.
   *
   * @param string $name
   * @return \App\Entities\Role
   *
   * @throws \App\Exceptions\Role\RoleNotFoundException
   */
  function findByName($name);

  /**
   * Update a role.
   *
   * @param \App\Entities\Role $role
   * @param array $attributes
   * @return \App\Entities\Role
   *
   * @throws \App\Exceptions\Role\CannotUpdateRoleException
   */
  function update(Role $role, $attributes);

  /**
   * Add a permission to the specified role.
   *
   * @param Role $role
   * @param Permission $permission
   * @return boolean
   */
  function addPermission(Role $role, Permission $permission);

  /**
   * Add multiple permissions to the specified role.
   *
   * @param Role $role
   * @param array $permissions
   * @return boolean
   */
  function addPermissions(Role $role, array $permissions = []);

  /**
   * Remove a permission from the specified role.
   *
   * @param Role $role
   * @param Permission $permission
   * @return boolean
   */
  function removePermission(Role $role, Permission $permission);

  /**
   * Remove multiple permissions from the specified role.
   *
   * @param Role $role
   * @param array $permissions
   * @return boolean
   */
  function removePermissions(Role $role, array $permissions = []);

  /**
   * Set all of the permissions of the specified role.
   *
   * @param Role $role
   * @param array $permissions
   * @return boolean
   */
  function setPermissions(Role $role, array $permissions = []);

  /**
   * Retrieve all of the permissions for the specified role.
   *
   * @param Role $role
   * @return \Illuminate\Database\Eloquent\Collection<\App\Entities\Permission>
   */
  function getPermissions(Role $role);
}
