<?php

namespace Domain\Permission;

use Domain\Permission\Contracts\Repositories\PermissionRepository as PermissionRepositoryInterface;
use Domain\Permission\Events\PermissionCreatedEvent;
use Domain\Permission\Events\PermissionUpdatedEvent;
use Domain\Permission\Exceptions\PermissionNotFoundException;
use Domain\Role\Role;
use Domain\User\User;
use Support\Repositories\AppRepository;
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
   * @param number|string|array $search
   * @param boolean $regex
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Permission\Permission>
   */
  public function find($parameter, $search, $regex = true)
  {
    $query = Permission::query();

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
  public function findByName($name)
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

  /**
   * Add a permission to the specified role.
   *
   * @param \Domain\Role\Role $role
   * @param \Domain\Permission\Permission $permission
   * @return \Domain\Role\Role
   */
  public function addPermissionToRole(Role $role, Permission $permission)
  {
    return $this->addPermissionsToRole($role, [
      $permission
    ]);
  }

  /**
   * Add a permission to the specified user.
   *
   * @param \Domain\User\User $user
   * @param \Domain\Permission\Permission $permission
   * @return \Domain\User\User
   */
  public function addPermissionToUser(User $user, Permission $permission)
  {
    return $this->addPermissionsToUser($user, [
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
  public function addPermissionsToRole(Role $role, $permissions = [])
  {
    return $role->attachPermissions($permissions);
  }

  /**
   * Add multiple permissions to the specified user.
   *
   * @param \Domain\User\User $user
   * @param \Illuminate\Support\Collection|array $permissions
   * @return \Domain\User\User
   */
  public function addPermissionsToUser(User $user, $permissions = [])
  {
    return $user->attachPermissions($permissions);
  }

  /**
   * Remove a permission from the specified role.
   *
   * @param \Domain\Role\Role $role
   * @param \Domain\Permission\Permission $permission
   * @return \Domain\Role\Role
   */
  public function removePermissionFromRole(Role $role, Permission $permission)
  {
    return $this->removePermissionsFromRole($role, [
      $permission
    ]);
  }

  /**
   * Remove a permission from the specified user.
   *
   * @param \Domain\User\User $user
   * @param \Domain\Permission\Permission $permission
   * @return \Domain\User\User
   */
  public function removePermissionFromUser(User $user, Permission $permission)
  {
    return $this->removePermissionsFromUser($user, [
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
  public function removePermissionsFromRole(Role $role, $permissions = [])
  {
    return $role->detachPermissions($permissions);
  }

  /**
   * Remove multiple permissions from the specified user.
   *
   * @param \Domain\User\User $user
   * @param \Illuminate\Support\Collection|array $permissions
   * @return \Domain\User\User
   */
  public function removePermissionsFromUser(User $user, $permissions = [])
  {
    return $user->detachPermissions($permissions);
  }

  /**
   * Set all of the permissions of the specified role.
   *
   * @param \Domain\Role\Role $role
   * @param \Illuminate\Support\Collection|array $permissions
   * @return \Domain\Role\Role
   */
  public function setRolePermissions(Role $role, $permissions = [])
  {
    return $role->syncPermissions($permissions);
  }

  /**
   * Set all of the permissions of the specified user.
   *
   * @param \Domain\User\User $user
   * @param \Illuminate\Support\Collection|array $permissions
   * @return \Domain\User\User
   */
  public function setUserPermissions(User $user, $permissions = [])
  {
    return $user->syncPermissions($permissions);
  }

  /**
   * Retrieve all of the permissions for the specified role.
   *
   * @param \Domain\Role\Role $role
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Permission\Permission>
   */
  public function getPermissionsForRole(Role $role)
  {
    return $role->permissions;
  }

  /**
   * Retrieve all of the permissions for the specified user.
   *
   * @param \Domain\User\User $user
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Permission\Permission>
   */
  public function getPermissionsForUser(User $user)
  {
    return $user->permissions;
  }

  /**
   * Retrieve all of the roles that have access to the specified permission.
   *
   * @param \Domain\Permission\Permission $permission
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Role\Role>
   */
  public function getRolesWithPermission(Permission $permission)
  {
    return $this->getRolesWithPermissions([$permission]);
  }

  /**
   * Retrieve all of the users that have access to the specified permission.
   *
   * @param \Domain\Permission\Permission $permission
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\User\User>
   */
  public function getUsersWithPermission(Permission $permission)
  {
    return $this->getUsersWithPermissions([$permission]);
  }

  /**
   * Retrieve all of the roles that have access to any of the specified permissions.
   *
   * @param \Illuminate\Support\Collection|array $permissions
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Role\Role>
   */
  public function getRolesWithPermissions($permissions = [])
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

  /**
   * Retrieve all of the users that have access to any of the specified permissions.
   *
   * @param \Illuminate\Support\Collection|array $permissions
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\User\User>
   */
  public function getUsersWithPermissions($permissions = [])
  {
    $query = User::query();

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
