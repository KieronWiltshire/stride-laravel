<?php

namespace App\Contracts\Repositories;

use App\Entities\Permission;

interface PermissionRepository
{
  /**
   * Retrieve all of the permissions.
   *
   * @return \Illuminate\Database\Eloquent\Collection<\App\Entities\Permission>
   */
  function all();

  /**
   * Retrieve all of the permissions.
   *
   * @param integer $limit
   * @param integer $offset
   * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<\App\Entities\Permission>
   *
   * @throws \App\Exceptions\Pagination\InvalidPaginationException
   */
  function allAsPaginated($limit = null, $offset = 1);

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
   * Find a permission by an unknown parameter.
   *
   * @param number|string $parameter
   * @param number|string $search
   * @param boolean $regex
   * @return \Illuminate\Database\Eloquent\Collection<\App\Entities\Permission>
   */
  function find($parameter, $search, $regex = true);

  /**
   * Find a permission by an unknown parameter.
   *
   * @param number|string $parameter
   * @param number|string $search
   * @param boolean $regex
   * @param integer $limit
   * @param integer $offset
   * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<\App\Entities\Permission>
   *
   * @throws \App\Exceptions\Pagination\InvalidPaginationException
   */
  function findAsPaginated($parameter, $search, $regex = true, $limit = null, $offset = 1);

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
