<?php

namespace Domain\Permission;

use Domain\Permission\Contracts\Repositories\PermissionRepository;
use Domain\Permission\Permission;

class PermissionService
{
  /**
   * @var \Domain\Permission\Contracts\Repositories\PermissionRepository
   */
  protected $permissionRepository;

  /**
   * Create a new user service instance.
   *
   * @param \Domain\Permission\Contracts\Repositories\PermissionRepository $permissionRepository
   */
  public function __construct(
    PermissionRepository $permissionRepository
  ) {
    $this->permissionRepository = $permissionRepository;
  }

  /**
   * Retrieve all of the permissions.
   *
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Permission\Permission>
   */
  function all()
  {
    return $this->permissionRepository->all();
  }

  /**
   * Create a new permission.
   *
   * @param array $attributes
   * @return \Domain\Permission\Permission
   *
   * @throws \Domain\Permission\Exceptions\CannotCreatePermissionException
   */
  function create($attributes)
  {
    return $this->permissionRepository->create($attributes);
  }

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
  function firstOrCreate($parameter, $search, $regex = true, $attributes = [])
  {
    return $this->permissionRepository->firstOrCreate($parameter, $search, $regex, $attributes);
  }

  /**
   * Find a permission by an unknown parameter.
   *
   * @param number|string $parameter
   * @param number|string|array $search
   * @param boolean $regex
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Permission\Permission>
   */
  function find($parameter, $search, $regex = true)
  {
    return $this->permissionRepository->find($parameter, $search, $regex);
  }

  /**
   * Find a permission by identifier.
   *
   * @param string $id
   * @return \Domain\Permission\Permission
   *
   * @throws \Domain\Permission\Exceptions\PermissionNotFoundException
   */
  function findById($id)
  {
    return $this->permissionRepository->findById($id);
  }

  /**
   * Find a permission by name.
   *
   * @param string $name
   * @return \Domain\Permission\Permission
   *
   * @throws \Domain\Permission\Exceptions\PermissionNotFoundException
   */
  function findByName($name)
  {
    return $this->permissionRepository->findByName($name);
  }

  /**
   * Update a permission.
   *
   * @param \Domain\Permission\Permission $permission
   * @param array $attributes
   * @return \Domain\Permission\Permission
   *
   * @throws \Domain\Permission\Exceptions\CannotUpdatePermissionException
   */
  function update(Permission $permission, $attributes)
  {
    return $this->permissionRepository->update($permission, $attributes);
  }

  /**
   * Retrieve an index of the permissions.
   *
   * @param integer $limit
   * @param integer $offset
   * @return \Illuminate\Pagination\LengthAwarePaginator<\Domain\Permission\Permission>
   */
  function index($limit = null, $offset = 1)
  {
    return $this->permissionRepository->paginate($limit, $offset)->all();
  }

  /**
   * Search for permissions with the specified search parameters.
   *
   * @param number|string $parameter
   * @param number|string $search
   * @param boolean $regex
   * @param integer $limit
   * @param integer $offset
   * @return \Illuminate\Pagination\LengthAwarePaginator<\Domain\Permission\Permission>
   */
  function search($parameter, $search, $regex = true, $limit = null, $offset = 1)
  {
    return $this->permissionRepository->paginate($limit, $offset)->find($parameter, $search, $regex);
  }
}