<?php

namespace Domain\Role;

use Domain\Role\Contracts\Repositories\RoleRepository as RoleRepositoryInterface;
use Domain\Permission\Permission;
use Domain\Role\Events\RoleCreatedEvent;
use Domain\Role\Events\RoleUpdatedEvent;
use Domain\Role\Exceptions\RoleNotFoundException;
use Infrastructure\Repositories\AppRepository;
use Domain\Role\Validators\RoleCreateValidator;
use Domain\Role\Validators\RoleUpdateValidator;
use Exception;

class RoleRepository extends AppRepository implements RoleRepositoryInterface
{
  /**
   * @var \Domain\Role\Validators\RoleCreateValidator
   */
  protected $roleCreateValidator;

  /**
   * @var \Domain\Role\Validators\RoleUpdateValidator
   */
  protected $roleUpdateValidator;

  /**
   * Create a new role repository instance.
   *
   * @param \Domain\Role\Validators\RoleCreateValidator $roleCreateValidator
   * @param \Domain\Role\Validators\RoleUpdateValidator $roleUpdateValidator
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
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Role\Role>
   */
  function all()
  {
    return $this->execute(Role::query());
  }

  /**
   * Create a new role.
   *
   * @param array $attributes
   * @return \Domain\Role\Role
   *
   * @throws \Domain\Role\Exceptions\CannotCreateRoleException
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
   * @return \Domain\Role\Role
   *
   * @throws \Domain\Role\Exceptions\CannotCreateRoleException
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
   * @param number|string|array $search
   * @param boolean $regex
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Role\Role>
   */
  function find($parameter, $search, $regex = true)
  {
    $query = Role::query();

    if (is_array($parameter)) {
      $query->whereIn($parameter, $search);
    } else {
      if ($regex) {
        $query->where($parameter, 'REGEXP', $search);
      } else {
        $query->where($parameter, $search);
      }
    }

    return $this->execute($query);
  }

  /**
   * Find a role by identifier.
   *
   * @param string $id
   * @return \Domain\Role\Role
   *
   * @throws \Domain\Role\Exceptions\RoleNotFoundException
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
   * @return \Domain\Role\Role
   *
   * @throws \Domain\Role\Exceptions\RoleNotFoundException
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
   * @param \Domain\Role\Role $role
   * @param array $attributes
   * @return \Domain\Role\Role
   *
   * @throws \Domain\Role\Exceptions\CannotUpdateRoleException
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
   * @param \Domain\Role\Role $role
   * @param \Domain\Permission\Permission $permission
   * @return \Domain\Role\Role
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
   * @param \Domain\Role\Role $role
   * @param \Illuminate\Support\Collection|array $permissions
   * @return \Domain\Role\Role
   */
  public function addPermissions(Role $role, $permissions = [])
  {
    return $role->attachPermissions($permissions);
  }

  /**
   * Remove a permission from the specified role.
   *
   * @param \Domain\Role\Role $role
   * @param \Domain\Permission\Permission $permission
   * @return \Domain\Role\Role
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
   * @param \Domain\Role\Role $role
   * @param \Illuminate\Support\Collection|array $permissions
   * @return \Domain\Role\Role
   */
  public function removePermissions(Role $role, $permissions = [])
  {
    return $role->detachPermissions($permissions);
  }

  /**
   * Set all of the permissions of the specified role.
   *
   * @param \Domain\Role\Role $role
   * @param \Illuminate\Support\Collection|array $permissions
   * @return \Domain\Role\Role
   */
  public function setPermissions(Role $role, $permissions = [])
  {
    return $role->syncPermissions($permissions);
  }

  /**
   * Retrieve all of the permissions for the specified role.
   *
   * @param \Domain\Role\Role $role
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Permission\Permission>
   */
  function getPermissions(Role $role)
  {
    return $role->permissions;
  }

  /**
   * Retrieve all of the roles that have access to the specified permission.
   *
   * @param \Domain\Permission\Permission $permission
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Role\Role>
   */
  function getRolesWithPermission(Permission $permission)
  {
    return $this->getRolesWithPermissions([$permission]);
  }

  /**
   * Retrieve all of the roles that have access to any of the specified permissions.
   *
   * @param \Illuminate\Support\Collection|array $permissions
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Role\Role>
   */
  function getRolesWithPermissions($permissions = [])
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
