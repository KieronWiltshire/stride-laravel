<?php

namespace Domain\Role;

use Domain\Role\Contracts\Repositories\RoleRepository;
use Domain\User\User;
use Illuminate\Support\Collection;

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
  public function all()
  {
    return $this->roleRepository->with(['permissions'])->all();
  }

  /**
   * Create a new role.
   *
   * @param array $attributes
   * @return \Domain\Role\Role
   *
   * @throws \Domain\Role\Exceptions\CannotCreateRoleException
   */
  public function create($attributes)
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
  public function firstOrCreate($parameter, $search, $regex = true, $attributes = [])
  {
    return $this->roleRepository->with(['permissions'])->firstOrCreate($parameter, $search, $regex, $attributes);
  }

  /**
   * Find a role by an unknown parameter.
   *
   * @param number|string $parameter
   * @param number|string|array $search
   * @param boolean $regex
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Role\Role>
   */
  public function find($parameter, $search, $regex = true)
  {
    return $this->roleRepository->with(['permissions'])->find($parameter, $search, $regex);
  }

  /**
   * Find a role by identifier.
   *
   * @param string $id
   * @return \Domain\Role\Role
   *
   * @throws \Domain\Role\Exceptions\RoleNotFoundException
   */
  public function findById($id)
  {
    return $this->roleRepository->with(['permissions'])->findById($id);
  }

  /**
   * Find a role by name.
   *
   * @param string $name
   * @return \Domain\Role\Role
   *
   * @throws \Domain\Role\Exceptions\RoleNotFoundException
   */
  public function findByName($name)
  {
    return $this->roleRepository->with(['permissions'])->findByName($name);
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
  public function update(Role $role, $attributes)
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
  public function index($limit = null, $offset = 1)
  {
    return $this->roleRepository->with(['permissions'])->paginate($limit, $offset)->all();
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
  public function search($parameter, $search, $regex = true, $limit = null, $offset = 1)
  {
    return $this->roleRepository->with(['permissions'])->paginate($limit, $offset)->find($parameter, $search, $regex);
  }

  /**
   * Add a role to the user.
   *
   * @param \Domain\User\User $user
   * @param \Domain\Role\Role $role
   * @return \Domain\User\User
   */
  public function addRoleToUser(User $user, Role $role)
  {
    return $this->roleRepository->addRoleToUser($user, $role);
  }

  /**
   * Add roles to the user.
   *
   * @param \Domain\User\User $user
   * @param \Illuminate\Support\Collection|array $roles
   * @return \Domain\User\User
   */
  public function addRolesToUser(User $user, $roles = [])
  {
    return $this->roleRepository->addRolesToUser($user, $roles);
  }

  /**
   * Remove a role from the user.
   *
   * @param \Domain\User\User $user
   * @param \Domain\Role\Role $role
   * @return \Domain\User\User
   */
  public function removeRoleFromUser(User $user, Role $role)
  {
    return $this->roleRepository->removeRolesFromUser($user, $role);
  }

  /**
   * Remove roles from the user.
   *
   * @param \Domain\User\User $user
   * @param \Illuminate\Support\Collection|array $roles
   * @return \Domain\User\User
   */
  public function removeRolesFromUser(User $user, $roles = [])
  {
    return $this->roleRepository->removeRolesFromUser($user, $roles);
  }

  /**
   * Set all of the roles of the specified user.
   *
   * @param \Domain\User\User $user
   * @param \Illuminate\Support\Collection|array $roles
   * @return \Domain\User\User
   */
  public function setUserRoles(User $user, $roles = [])
  {
    return $this->roleRepository->setUserRoles($user, $roles);
  }

  /**
   * Retrieve all of the roles for the specified user.
   *
   * @param \Domain\User\User $user
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Role\Role>
   */
  public function getRolesFromUser(User $user)
  {
    return $this->roleRepository->getRolesFromUser($user);
  }

  /**
   * Determine if the given user has the specified role assigned.
   *
   * @param \Domain\User\User $user
   * @param \Domain\Role\Role $role
   * @return bool
   */
  public function userHasRole(User $user, Role $role)
  {
    return ($this->getRoles($user)->where('id', $role->id)->count() > 0);
  }

  /**
   * Determine if the given user has the specified roles assigned.
   *
   * @param \Domain\User\User $user
   * @param \Illuminate\Support\Collection|array $roles
   * @return bool
   */
  public function userHasRoles(User $user, $roles)
  {
    $roleIds = ($roles instanceof Collection ? $roles : collect($roles))
      ->map->only(['id'])
      ->flatten()
      ->all();

    return ($this->getRoles($user)->whereIn('id', $roleIds)->count() > 0);
  }

  /**
   * Retrieve all of the users that are associated with the specified role.
   *
   * @param \Domain\Role\Role $role
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\User\User>
   */
  public function getUsersWithRole(Role $role)
  {
    return $this->roleRepository->getUsersWithRole($role);
  }

  /**
   * Retrieve all of the users that are associated with any of the specified roles.
   *
   * @param \Illuminate\Support\Collection|array $roles
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\User\User>
   */
  public function getUsersWithRoles($roles = [])
  {
    return $this->roleRepository->getUsersWithRoles($roles);
  }
}