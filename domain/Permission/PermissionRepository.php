<?php

namespace Domain\Permission;

use Domain\Permission\Contracts\Repositories\PermissionRepository as PermissionRepositoryInterface;
use Domain\Permission\Events\PermissionCreatedEvent;
use Domain\Permission\Events\PermissionUpdatedEvent;
use Domain\Permission\Exceptions\PermissionNotFoundException;
use Infrastructure\Repositories\AppRepository;
use Domain\Permission\Validators\PermissionCreateValidator;
use Domain\Permission\Validators\PermissionUpdateValidator;
use Exception;

class PermissionRepository extends AppRepository implements PermissionRepositoryInterface
{
  /**
   * @var \Domain\Permission\Validators\PermissionCreateValidator
   */
  protected $permissionCreateValidator;

  /**
   * @var \Domain\Permission\Validators\PermissionUpdateValidator
   */
  protected $permissionUpdateValidator;

  /**
   * Create a new role repository instance.
   *
   * @param \Domain\Permission\Validators\PermissionCreateValidator $permissionCreateValidator
   * @param \Domain\Permission\Validators\PermissionUpdateValidator $permissionUpdateValidator
   */
  public function __construct(
    PermissionCreateValidator $permissionCreateValidator,
    PermissionUpdateValidator $permissionUpdateValidator
  ) {
    $this->permissionCreateValidator = $permissionCreateValidator;
    $this->permissionUpdateValidator = $permissionUpdateValidator;
  }

  /**
   * Retrieve all of the permissions.
   *
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Permission\Permission>
   */
  public function all()
  {
    return $this->execute(Permission::query());
  }

  /**
   * Create a new permission.
   *
   * @param array $attributes
   * @return \Domain\Permission\Permission
   *
   * @throws \Domain\Permission\Exceptions\CannotCreatePermissionException
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
  public function firstOrCreate($parameter, $search, $regex = true, $attributes = [])
  {
    $query = Permission::query();

    if ($regex) {
      $query->where($parameter, 'REGEXP', $search);
    } else {
      $query->where($parameter, $search);
    }

    $permission = $this->execute($query, true);

    return ($permission) ? $permission : $this->create($attributes);
  }

  /**
   * Find a permission by an unknown parameter.
   *
   * @param number|string $parameter
   * @param number|string $search
   * @param boolean $regex
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Permission\Permission>
   */
  public function find($parameter, $search, $regex = true)
  {
    $query = Permission::query();

    if ($regex) {
      $query->where($parameter, 'REGEXP', $search);
    } else {
      $query->where($parameter, $search);
    }

    return $this->execute($query);
  }

  /**
   * Find a permission by identifier.
   *
   * @param string $id
   * @return \Domain\Permission\Permission
   *
   * @throws \Domain\Permission\Exceptions\PermissionNotFoundException
   */
  public function findById($id)
  {
    $permission = $this->execute(Permission::where('id', $id), true);

    if (!$permission) {
      throw new PermissionNotFoundException();
    }

    return $permission;
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
    $role = $this->execute(Permission::where('name', $name), true);

    if (!$role) {
      throw new PermissionNotFoundException();
    }

    return $role;
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
  public function update(Permission $permission, $attributes)
  {
    $this->permissionUpdateValidator->validate($attributes);

    // TODO:

    if ($permission->save()) {
      event(new PermissionUpdatedEvent($permission, $attributes));

      return $permission;
    }
  }
}
