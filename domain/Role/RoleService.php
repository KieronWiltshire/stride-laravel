<?php

namespace Domain\Role;

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
}