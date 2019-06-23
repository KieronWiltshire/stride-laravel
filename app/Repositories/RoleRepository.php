<?php

namespace App\Repositories;

use App\Contracts\Repositories\RoleRepository as RoleRepositoryInterface;
use App\Entities\Permission;
use App\Entities\Role;
use App\Events\Role\RoleCreatedEvent;
use App\Events\Role\RoleUpdatedEvent;
use App\Exceptions\Role\RoleNotFoundException;
use App\Validators\Pagination\PaginationValidator;
use App\Validators\Role\RoleCreateValidator;
use App\Validators\Role\RoleUpdateValidator;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;

class RoleRepository implements RoleRepositoryInterface
{
  /**
   * @var \App\Validators\Pagination\PaginationValidator
   */
  protected $paginationValidator;

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
   * @param \App\Validators\Pagination\PaginationValidator $paginationValidator
   * @param \App\Validators\Role\RoleCreateValidator $roleCreateValidator
   * @param \App\Validators\Role\RoleUpdateValidator $roleUpdateValidator
   */
  public function __construct(
    PaginationValidator $paginationValidator,
    RoleCreateValidator $roleCreateValidator,
    RoleUpdateValidator $roleUpdateValidator
  ) {
    $this->paginationValidator = $paginationValidator;
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
    return Role::all();
  }

  /**
   * Retrieve all of the roles.
   *
   * @param integer $limit
   * @param integer $offset
   * @return \Illuminate\Pagination\LengthAwarePaginator<\App\Entities\Role>
   *
   * @throws \App\Exceptions\Pagination\InvalidPaginationException
   */
  function allAsPaginated($limit = null, $offset = 1)
  {
    $this->paginationValidator->validate([
      'limit' => $limit,
      'offset' => $offset
    ]);

    if ($limit) {
      return Role::paginate($limit, ['*'], 'page', $offset);
    } else {
      $roles = Role::all();

      return new LengthAwarePaginator($roles->all(), $roles->count(), max($roles->count(), 1), 1);
    }
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

    $role = $query->first();

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

    return $query->get();
  }

  /**
   * Find a role by an unknown parameter.
   *
   * @param number|string $parameter
   * @param number|string $search
   * @param boolean $regex
   * @param integer $limit
   * @param integer $offset
   * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<\App\Entities\Role>
   *
   * @throws \App\Exceptions\Pagination\InvalidPaginationException
   */
  function findAsPaginated($parameter, $search, $regex = true, $limit = null, $offset = 1)
  {
    $this->paginationValidator->validate([
      'limit' => $limit,
      'offset' => $offset
    ]);

    $query = Role::query();

    if ($regex) {
      $query->where($parameter, 'REGEXP', $search)->get();
    } else {
      $query->where($parameter, $search)->get();
    }

    if ($limit) {
      return $query->paginate($limit, ['*'], 'page', $offset);
    } else {
      $roles = $query->get();

      return new LengthAwarePaginator($roles->all(), $roles->count(), max($roles->count(), 1), 1);
    }
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
    $role = Role::find($id);

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
    $role = Role::where('name', $name)->first();

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

    return $query->get();
  }

  /**
   * Retrieve all of the roles that have access to the specified permissions.
   *
   * @param \App\Entities\Permission $permission
   * @param integer $limit
   * @param integer $offset
   * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<\App\Entities\Role>
   *
   * @throws \App\Exceptions\Pagination\InvalidPaginationException
   */
  function getRolesWithPermissionAsPaginated(Permission $permission, $limit = null, $offset = 1)
  {
   return $this->getRolesWithPermissionsAsPaginated([$permission], $limit, $offset);
  }

  /**
   * Retrieve all of the roles that have access to any of the specified permissions.
   *
   * @param array $permissions
   * @param integer $limit
   * @param integer $offset
   * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<\App\Entities\Role>
   *
   * @throws \App\Exceptions\Pagination\InvalidPaginationException
   */
  function getRolesWithPermissionsAsPaginated(array $permissions, $limit = null, $offset = 1)
  {
    $query = Role::query();

    foreach ($permissions as $index => $permission) {
      if ($index <= 0) {
        $query->whereRoleIs($permission->name);
      } else {
        $query->orWhereRoleIs($permission->name);
      }
    }

    if ($limit) {
      return $query->paginate($limit, ['*'], 'page', $offset);
    } else {
      $roles = $query->get();

      return new LengthAwarePaginator($roles->all(), $roles->count(), max($roles->count(), 1), 1);
    }
  }
}
