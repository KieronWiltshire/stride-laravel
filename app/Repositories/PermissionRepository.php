<?php

namespace App\Repositories;

use App\Contracts\Repositories\PermissionRepository as PermissionRepositoryInterface;
use App\Entities\Permission;
use App\Events\Permission\PermissionCreatedEvent;
use App\Events\Permission\PermissionUpdatedEvent;
use App\Exceptions\Permission\PermissionNotFoundException;
use App\Validation\Pagination\PaginationValidator;
use App\Validation\Permission\PermissionCreateValidator;
use App\Validation\Permission\PermissionUpdateValidator;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;

class PermissionRepository implements PermissionRepositoryInterface
{
  /**
   * @var \App\Validation\Pagination\PaginationValidator
   */
  protected $paginationValidator;

  /**
   * @var \App\Validation\Permission\PermissionCreateValidator
   */
  protected $permissionCreateValidator;

  /**
   * @var \App\Validation\Permission\PermissionUpdateValidator
   */
  protected $permissionUpdateValidator;

  /**
   * Create a new role repository instance.
   *
   * @param \App\Validation\Pagination\PaginationValidator $paginationValidator
   * @param \App\Validation\Permission\PermissionCreateValidator $permissionCreateValidator
   * @param \App\Validation\Permission\PermissionUpdateValidator $permissionUpdateValidator
   */
  public function __construct(
    PaginationValidator $paginationValidator,
    PermissionCreateValidator $permissionCreateValidator,
    PermissionUpdateValidator $permissionUpdateValidator
  ) {
    $this->paginationValidator = $paginationValidator;
    $this->permissionCreateValidator = $permissionCreateValidator;
    $this->permissionUpdateValidator = $permissionUpdateValidator;
  }

  /**
   * Retrieve all of the permissions.
   *
   * @return \Illuminate\Database\Eloquent\Collection<\App\Entities\Permission>
   */
  public function all()
  {
    return Permission::all();
  }

  /**
   * Retrieve all of the permissions.
   *
   * @param integer $limit
   * @param integer $offset
   * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<\App\Entities\Permission>
   *
   * @throws \App\Exceptions\Pagination\InvalidPaginationException
   */
  public function allAsPaginated($limit = null, $offset = 1)
  {
    $this->paginationValidator->validate([
      'limit' => $limit,
      'offset' => $offset
    ]);

    if ($limit) {
      return Permission::paginate($limit, ['*'], 'page', $offset);
    } else {
      $permissions = Permission::all();

      return new LengthAwarePaginator($permissions->all(), $permissions->count(), max($permissions->count(), 1), 1);
    }
  }

  /**
   * Create a new permission.
   *
   * @param array $attributes
   * @return \App\Entities\Permission
   *
   * @throws \App\Exceptions\Permission\CannotCreatePermissionException
   */
  public function create($attributes)
  {
    $this->permissionCreateValidator->validate($attributes);

    if ($permission = Permission::create($attributes)) {
      event(new PermissionCreatedEvent($permission));

      return $permission;
    }

    throw new Exception();
  }

  /**
   * Find a permission by an unknown parameter.
   *
   * @param number|string $parameter
   * @param number|string $search
   * @param boolean $regex
   * @return \Illuminate\Database\Eloquent\Collection<\App\Entities\Permission>
   */
  public function find($parameter, $search, $regex = true)
  {
    $query = Permission::query();

    if ($regex) {
      $query->where($parameter, 'REGEXP', $search)->get();
    } else {
      $query->where($parameter, $search)->get();
    }

    return $query->get();
  }

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
  public function findAsPaginated($parameter, $search, $regex = true, $limit = null, $offset = 1)
  {
    $this->paginationValidator->validate([
      'limit' => $limit,
      'offset' => $offset
    ]);

    $query = Permission::query();

    if ($regex) {
      $query->where($parameter, 'REGEXP', $search)->get();
    } else {
      $query->where($parameter, $search)->get();
    }

    if ($limit) {
      return $query->paginate($limit, ['*'], 'page', $offset);
    } else {
      $permissions = $query->get();

      return new LengthAwarePaginator($permissions->all(), $permissions->count(), max($permissions->count(), 1), 1);
    }
  }

  /**
   * Find a permission by identifier.
   *
   * @param string $id
   * @return \App\Entities\Permission
   *
   * @throws \App\Exceptions\Permission\PermissionNotFoundException
   */
  public function findById($id)
  {
    $permission = Permission::find($id);

    if (!$permission) {
      throw new PermissionNotFoundException();
    }

    return $permission;
  }

  /**
   * Update a permission.
   *
   * @param \App\Entities\Permission $permission
   * @param array $attributes
   * @return \App\Entities\Permission
   *
   * @throws \App\Exceptions\Permission\CannotUpdatePermissionException
   */
  public function update(Permission $permission, $attributes)
  {
    if ($permission instanceof Permission) {
      $this->permissionUpdateValidator->validate($attributes);

      // TODO:

      if ($permission->save()) {
        event(new PermissionUpdatedEvent($permission, $attributes));

        return $permission;
      }
    }

    throw new Exception();
  }
}
