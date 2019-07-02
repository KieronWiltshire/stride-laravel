<?php

namespace Domain\Permission\Contracts\Repositories;

use Infrastructure\Contracts\Repositories\AppRepository;
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
   * @param number|string $search
   * @param boolean $regex
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Permission\Permission>
   */
  function find($parameter, $search, $regex = true);

  /**
   * Find a permission by identifier.
   *
   * @param string $id
   * @return \App\Entities\Permission
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
}
