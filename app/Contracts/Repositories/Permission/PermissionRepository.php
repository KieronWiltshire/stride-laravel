<?php

namespace App\Contracts\Repositories\Permission;

use App\Contracts\Repositories\AppRepository;
use App\Entities\Permission;

interface PermissionRepository extends AppRepository
{
  /**
   * Retrieve all of the permissions.
   *
   * @return \Illuminate\Database\Eloquent\Collection<\App\Entities\Permission>
   */
  function all();

  /**
   * Create a new permission.
   *
   * @param array $attributes
   * @return \App\Entities\Permission
   *
   * @throws \App\Exceptions\Permission\CannotCreatePermissionException
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
   * @return \App\Entities\Permission
   *
   * @throws \App\Exceptions\Permission\CannotCreatePermissionException
   */
  function firstOrCreate($parameter, $search, $regex = true, $attributes = []);

  /**
   * Find a permission by an unknown parameter.
   *
   * @param number|string $parameter
   * @param number|string $search
   * @param boolean $regex
   * @return \Illuminate\Database\Eloquent\Collection<\App\Entities\Permission>
   */
  function find($parameter, $search, $regex = true);

  /**
   * Find a permission by identifier.
   *
   * @param string $id
   * @return \App\Entities\Permission
   *
   * @throws \App\Exceptions\Permission\PermissionNotFoundException
   */
  function findById($id);

  /**
   * Find a permission by name.
   *
   * @param string $name
   * @return \App\Entities\Permission
   *
   * @throws \App\Exceptions\Permission\PermissionNotFoundException
   */
  function findByName($name);

  /**
   * Update a permission.
   *
   * @param \App\Entities\Permission $permission
   * @param array $attributes
   * @return \App\Entities\Permission
   *
   * @throws \App\Exceptions\Permission\CannotUpdatePermissionException
   */
  function update(Permission $permission, $attributes);

}
