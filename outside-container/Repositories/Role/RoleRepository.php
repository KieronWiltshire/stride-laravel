<?php

namespace App\Repositories\Role;

use App\Contracts\Repositories\Role\RoleRepository as RoleRepositoryInterface;
use App\Entities\Permission;
use App\Entities\Role;
use App\Events\Role\RoleCreatedEvent;
use App\Events\Role\RoleUpdatedEvent;
use App\Exceptions\Role\RoleNotFoundException;
use App\Repositories\AppRepository;
use App\Validators\Pagination\PaginationValidator;
use App\Validators\Role\RoleCreateValidator;
use App\Validators\Role\RoleUpdateValidator;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;

class RoleRepository extends AppRepository implements RoleRepositoryInterface
{
  /**
   * @var \App\Validators\Role\RoleCreateValidator
   */
  protected $roleCreateValidator;

  /**
   * @var \App\Validators\Role\RoleUpdateValidator
   */
  protected $roleUpdateValidator;

  /**
   * Create a new role repository instance.
   *
   * @param \App\Validators\Role\RoleCreateValidator $roleCreateValidator
   * @param \App\Validators\Role\RoleUpdateValidator $roleUpdateValidator
   */
  public function __construct(
    RoleCreateValidator $roleCreateValidator,
    RoleUpdateValidator $roleUpdateValidator
  ) {
    $this->roleCreateValidator = $roleCreateValidator;
    $this->roleUpdateValidator = $roleUpdateValidator;
  }

  /**
   * Retrieve all of the roles.
   *
   * @return \Illuminate\Database\Eloquent\Collection<\App\Entities\Role>
   */
  function all()
  {
    return $this->execute(Role::query());
  }

  /**
   * Create a new role.
   *
   * @param array $attributes
   * @return \App\Entities\Role
   *
   * @throws \App\Exceptions\Role\CannotCreateRoleException
   */
  function create($attributes)
  {
    $this->roleCreateValidator->validate($attributes);

    if ($role = Role::create($attributes)) {
      event(new RoleCreatedEvent($role));

      return $role;
    }

    throw new Exception();
  }

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
  public function firstOrCreate($parameter, $search, $regex = true, $attributes = [])
  {
    $query = Role::query();

    if ($regex) {
      $query->where($parameter, 'REGEXP', $search);
    } else {
      $query->where($parameter, $search);
    }

    $role = $this->execute($query, true);

    return ($role) ? $role : $this->create($attributes);
  }

  /**
   * Find a role by an unknown parameter.
   *
   * @param number|string $parameter
   * @param number|string $search
   * @param boolean $regex
   * @return \Illuminate\Database\Eloquent\Collection<\App\Entities\Role>
   */
  function find($parameter, $search, $regex = true)
  {
    $query = Role::query();

    if ($regex) {
      $query->where($parameter, 'REGEXP', $search);
    } else {
      $query->where($parameter, $search);
    }

    return $this->execute($query);
  }

  /**
   * Find a role by identifier.
   *
   * @param string $id
   * @return \App\Entities\Role
   *
   * @throws \App\Exceptions\Role\RoleNotFoundException
   */
  function findById($id)
  {
    $role = $this->execute(Role::where('id', $id), true);

    if (!$role) {
      throw new RoleNotFoundException();
    }

    return $role;
  }

  /**
   * Find a role by name.
   *
   * @param string $name
   * @return \App\Entities\Role
   *
   * @throws \App\Exceptions\Role\RoleNotFoundException
   */
  function findByName($name)
  {
    $role = $this->execute(Role::where('name', $name), true);

    if (!$role) {
      throw new RoleNotFoundException();
    }

    return $role;
  }

  /**
   * Update a role.
   *
   * @param \App\Entities\Role $role
   * @param array $attributes
   * @return \App\Entities\Role
   *
   * @throws \App\Exceptions\Role\CannotUpdateRoleException
   */
  function update(Role $role, $attributes)
  {
    $this->roleUpdateValidator->validate($attributes);

    // TODO:

    if ($role->save()) {
      event(new RoleUpdatedEvent($role, $attributes));

      return $role;
    }
  }

  /**
   * Add a permission to the specified role.
   *
   * @param \App\Entities\Role $role
   * @param \App\Entities\Permission $permission
   * @return \App\Entities\Role
   */
  public function addPermission(Role $role, Permission $permission)
  {
    return $this->addPermissions($role, [
      $permission
    ]);
  }

  /**
   * Add multiple permissions to the specified role.
   *
   * @param \App\Entities\Role $role
   * @param array $permissions
   * @return \App\Entities\Role
   */
  public function addPermissions(Role $role, array $permissions = [])
  {
    return $role->attachPermissions($permissions);
  }

  /**
   * Remove a permission from the specified role.
   *
   * @param \App\Entities\Role $role
   * @param \App\Entities\Permission $permission
   * @return \App\Entities\Role
   */
  public function removePermission(Role $role, Permission $permission)
  {
    return $this->removePermissions($role, [
      $permission
    ]);
  }

  /**
   * Remove multiple permissions from the specified role.
   *
   * @param \App\Entities\Role $role
   * @param array $permissions
   * @return \App\Entities\Role
   */
  public function removePermissions(Role $role, array $permissions = [])
  {
    return $role->detachPermissions($permissions);
  }

  /**
   * Set all of the permissions of the specified role.
   *
   * @param \App\Entities\Role $role
   * @param array $permissions
   * @return \App\Entities\Role
   */
  public function setPermissions(Role $role, array $permissions = [])
  {
    return $role->syncPermissions($permissions);
  }

  /**
   * Retrieve all of the permissions for the specified role.
   *
   * @param \App\Entities\Role $role
   * @return \Illuminate\Database\Eloquent\Collection<\App\Entities\Permission>
   */
  function getPermissions(Role $role)
  {
    return $role->permissions;
  }

  /**
   * Retrieve all of the roles that have access to the specified permission.
   *
   * @param \App\Entities\Permission $permission
   * @return \Illuminate\Database\Eloquent\Collection<\App\Entities\Role>
   */
  function getRolesWithPermission(Permission $permission)
  {
    return $this->getRolesWithPermissions([$permission]);
  }

  /**
   * Retrieve all of the roles that have access to any of the specified permissions.
   *
   * @param array $permissions
   * @return \Illuminate\Database\Eloquent\Collection<\App\Entities\Role>
   */
  function getRolesWithPermissions(array $permissions = [])
  {
    $query = Role::query();

    foreach ($permissions as $index => $permission) {
      if ($index <= 0) {
        $query->wherePermissionIs($permission->name);
      } else {
        $query->orWherePermissionIs($permission->name);
      }
    }

    return $this->execute($query);
  }
}
