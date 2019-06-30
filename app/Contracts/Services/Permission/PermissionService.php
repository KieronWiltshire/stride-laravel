<?php

namespace App\Contracts\Services\Permission;

use App\Entities\Permission;

interface PermissionService
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

  /**
   * Retrieve an index of the permissions.
   *
   * @param integer $limit
   * @param integer $offset
   * @return \Illuminate\Pagination\LengthAwarePaginator<\App\Entities\Permission>
   */
  function index($limit = null, $offset = 1);

  /**
   * Search for permissions with the specified search parameters.
   *
   * @param number|string $parameter
   * @param number|string $search
   * @param boolean $regex
   * @param integer $limit
   * @param integer $offset
   * @return \Illuminate\Pagination\LengthAwarePaginator<\App\Entities\Permission>
   */
  function search($parameter, $search, $regex = true, $limit = null, $offset = 1);
}