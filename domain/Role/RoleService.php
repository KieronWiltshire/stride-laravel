<?php

namespace Domain\Role;

use Domain\Permission\Permission;
use Domain\Role\Contracts\Repositories\RoleRepository;

class RoleService
{
  /**
   * @var \Domain\Role\Contracts\Repositories\RoleRepository
   */
  protected $roleRepository;

  /**
   * Create a new user service instance.
   *
   * @param \Domain\Role\Contracts\Repositories\RoleRepository $roleRepository
   */
  public function __construct(
    RoleRepository $roleRepository
  ) {
    $this->roleRepository = $roleRepository;
  }

  /**
   * Retrieve all of the roles.
   *
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Role\Role>
   */
  function all()
  {
    return $this->roleRepository->all();
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
    return $this->roleRepository->create($attributes);
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
  function firstOrCreate($parameter, $search, $regex = true, $attributes = [])
  {
    return $this->roleRepository->firstOrCreate($parameter, $search, $regex, $attributes);
  }

  /**
   * Find a role by an unknown parameter.
   *
   * @param number|string $parameter
   * @param number|string $search
   * @param boolean $regex
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Role\Role>
   */
  function find($parameter, $search, $regex = true)
  {
    return $this->roleRepository->find($parameter, $search, $regex);
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
    return $this->roleRepository->findById($id);
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
    return $this->roleRepository->findByName($name);
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
    return $this->update($role, $attributes);
  }

  /**
   * Retrieve an index of the roles.
   *
   * @param integer $limit
   * @param integer $offset
   * @return \Illuminate\Pagination\LengthAwarePaginator<\Domain\Role\Role>
   */
  function index($limit = null, $offset = 1)
  {
    return $this->roleRepository->paginate($limit, $offset)->all();
  }

  /**
   * Search for roles with the specified search parameters.
   *
   * @param number|string $parameter
   * @param number|string $search
   * @param boolean $regex
   * @param integer $limit
   * @param integer $offset
   * @return \Illuminate\Pagination\LengthAwarePaginator<\Domain\Role\Role>
   */
  function search($parameter, $search, $regex = true, $limit = null, $offset = 1)
  {
    return $this->roleRepository->paginate($limit, $offset)->find($parameter, $search, $regex);
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
    return $this->roleRepository->addPermission($role, $permission);
  }

  /**
   * Add multiple permissions to the specified role.
   *
   * @param \Domain\Role\Role $role
   * @param array $permissions
   * @return \Domain\Role\Role
   */
  public function addPermissions(Role $role, array $permissions = [])
  {
    return $this->roleRepository->addPermissions($role, $permissions);
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
    return $this->roleRepository->removePermission($role, $permission);
  }

  /**
   * Remove multiple permissions from the specified role.
   *
   * @param \Domain\Role\Role $role
   * @param array $permissions
   * @return \Domain\Role\Role
   */
  public function removePermissions(Role $role, array $permissions = [])
  {
    return $this->roleRepository->removePermissions($role, $permissions);
  }

  /**
   * Set all of the permissions of the specified role.
   *
   * @param \Domain\Role\Role $role
   * @param array $permissions
   * @return \Domain\Role\Role
   */
  public function setPermissions(Role $role, array $permissions = [])
  {
    return $this->roleRepository->setPermissions($role, $permissions);
  }

  /**
   * Retrieve all of the permissions for the specified role.
   *
   * @param \Domain\Role\Role $role
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Permission\Permission>
   */
  function getPermissions(Role $role)
  {
    return $this->roleRepository->getPermissions($role);
  }

  /**
   * Determine if the given role has the specified permission assigned.
   *
   * @param \Domain\Role\Role $role
   * @param \Domain\Permission\Permission $permission
   * @return bool
   */
  function hasPermission(Role $role, Permission $permission)
  {
    return ($this->getPermissions($role)->where('id', $permission->id)->count() > 0);
  }

  /**
   * Determine if the given role has the specified permissions assigned.
   *
   * @param \Domain\Role\Role $role
   * @param array $permissions
   * @return bool
   */
  function hasPermissions(Role $role, array $permissions)
  {
    $permissionIds = collect($permissions)
      ->map->only(['id'])
      ->flatten()
      ->all();

    return ($this->getPermissions($role)->whereIn('id', $permissionIds)->count() > 0);
  }

  /**
   * Retrieve all of the roles that have access to the specified permission.
   *
   * @param \Domain\Permission\Permission $permission
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Role\Role>
   */
  function getRolesWithPermission(Permission $permission)
  {
    return $this->roleRepository->getRolesWithPermission($permission);
  }

  /**
   * Retrieve all of the roles that have access to any of the specified permissions.
   *
   * @param array $permissions
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Role\Role>
   */
  function getRolesWithPermissions(array $permissions = [])
  {
    return $this->roleRepository->getRolesWithPermissions($permissions);
  }
}