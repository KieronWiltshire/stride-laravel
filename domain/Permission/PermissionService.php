<?php

namespace Domain\Permission;

use Domain\Permission\Contracts\Repositories\PermissionRepository;
use Domain\Permission\Permission;
use Domain\Role\Role;
use Domain\User\User;
use Illuminate\Support\Collection;

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
  public function all()
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
  public function create($attributes)
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
  public function firstOrCreate($parameter, $search, $regex = true, $attributes = [])
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
  public function find($parameter, $search, $regex = true)
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
  public function findById($id)
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
  public function findByName($name)
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
  public function update(Permission $permission, $attributes)
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
  public function index($limit = null, $offset = 1)
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
  public function search($parameter, $search, $regex = true, $limit = null, $offset = 1)
  {
    return $this->permissionRepository->paginate($limit, $offset)->find($parameter, $search, $regex);
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
    return $this->permissionRepository->addPermissionToRole($role, $permission);
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
    return $this->permissionRepository->addPermissionToUser($user, $permission);
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
    return $this->permissionRepository->addPermissionsToRole($role, $permissions);
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
    return $this->permissionRepository->addPermissionsToUser($user, $permissions);
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
    return $this->permissionRepository->removePermissionFromRole($role, $permission);
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
    return $this->permissionRepository->removePermissionFromUser($user, $permission);
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
    return $this->permissionRepository->removePermissionsFromRole($role, $permissions);
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
    return $this->permissionRepository->removePermissionsFromUser($user, $permissions);
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
    return $this->permissionRepository->setRolePermissions($role, $permissions);
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
    return $this->permissionRepository->setUserPermissions($user, $permissions);
  }

  /**
   * Retrieve all of the permissions for the specified role.
   *
   * @param \Domain\Role\Role $role
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Permission\Permission>
   */
  public function getPermissionsFromRole(Role $role)
  {
    return $this->permissionRepository->getPermissionsFromRole($role);
  }

  /**
   * Retrieve all of the permissions for the specified user.
   *
   * @param \Domain\User\User $user
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Permission\Permission>
   */
  public function getPermissionsFromUser(User $user)
  {
    return $this->permissionRepository->getPermissionsFromUser($user);
  }

  /**
   * Determine if the given role has the specified permission assigned.
   *
   * @param \Domain\Role\Role $role
   * @param \Domain\Permission\Permission $permission
   * @return bool
   */
  public function roleHasPermission(Role $role, Permission $permission)
  {
    return ($this->getPermissionsFromRole($role)->where('id', $permission->id)->count() > 0);
  }

  /**
   * Determine if the given user has the specified permission assigned.
   *
   * @param \Domain\User\User $user
   * @param \Domain\Permission\Permission $permission
   * @return bool
   */
  public function userHasPermission(User $user, Permission $permission)
  {
    return ($this->getPermissionsFromUser($user)->where('id', $permission->id)->count() > 0);
  }

  /**
   * Determine if the given role has the specified permissions assigned.
   *
   * @param \Domain\Role\Role $role
   * @param \Illuminate\Support\Collection|array $permissions
   * @return bool
   */
  public function roleHasPermissions(Role $role, $permissions)
  {
    $permissionIds = ($permissions instanceof Collection ? $permissions : collect($permissions))
      ->map->only(['id'])
      ->flatten()
      ->all();

    return ($this->getPermissionsFromRole($role)->whereIn('id', $permissionIds)->count() > 0);
  }

  /**
   * Determine if the given user has the specified permissions assigned.
   *
   * @param \Domain\User\User $user
   * @param \Illuminate\Support\Collection|array $permissions
   * @return bool
   */
  public function userHasPermissions(User $user, $permissions)
  {
    $permissionIds = ($permissions instanceof Collection ? $permissions : collect($permissions))
      ->map->only(['id'])
      ->flatten()
      ->all();

    return ($this->getPermissionsFromUser($user)->whereIn('id', $permissionIds)->count() > 0);
  }

  /**
   * Retrieve all of the roles that have access to the specified permission.
   *
   * @param \Domain\Permission\Permission $permission
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Role\Role>
   */
  public function getRolesWithPermission(Permission $permission)
  {
    return $this->permissionRepository->getRolesWithPermission($permission);
  }

  /**
   * Retrieve all of the users that have access to the specified permission.
   *
   * @param \Domain\Permission\Permission $permission
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\User\User>
   */
  public function getUsersWithPermission(Permission $permission)
  {
    return $this->permissionRepository->getUsersWithPermission($permission);
  }

  /**
   * Retrieve all of the roles that have access to any of the specified permissions.
   *
   * @param \Illuminate\Support\Collection|array $permissions
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Role\Role>
   */
  public function getRolesWithPermissions($permissions = [])
  {
    return $this->permissionRepository->getRolesWithPermissions($permissions);
  }

  /**
   * Retrieve all of the users that have access to any of the specified permissions.
   *
   * @param \Illuminate\Support\Collection|array $permissions
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\User\User>
   */
  public function getUsersWithPermissions($permissions = [])
  {
    return $this->permissionRepository->getUsersWithPermissions($permissions);
  }

}