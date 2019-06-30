<?php

namespace App\Services\Role;

use App\Contracts\Services\Role\RoleService as RoleServiceInterface;
use App\Entities\Role;
use App\Repositories\Role\RoleRepository;

class RoleService implements RoleServiceInterface
{
  /**
   * @var \App\Contracts\Repositories\Role\RoleRepository
   */
  protected $roleRepository;

  /**
   * Create a new user service instance.
   *
   * @param \App\Contracts\Repositories\Role\RoleRepository $roleRepository
   */
  public function __construct(
    RoleRepository $roleRepository
  ) {
    $this->roleRepository = $roleRepository;
  }

  /**
   * Retrieve all of the roles.
   *
   * @return \Illuminate\Database\Eloquent\Collection<\App\Entities\Role>
   */
  function all()
  {
    return $this->roleRepository->all();
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
   * @return \App\Entities\Role
   *
   * @throws \App\Exceptions\Role\CannotCreateRoleException
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
   * @return \Illuminate\Database\Eloquent\Collection<\App\Entities\Role>
   */
  function find($parameter, $search, $regex = true)
  {
    return $this->roleRepository->find($parameter, $search, $regex);
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
    return $this->roleRepository->findById($id);
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
    return $this->roleRepository->findByName($name);
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
    return $this->update($role, $attributes);
  }

  /**
   * Retrieve an index of the roles.
   *
   * @param integer $limit
   * @param integer $offset
   * @return \Illuminate\Pagination\LengthAwarePaginator<\App\Entities\Role>
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
   * @return \Illuminate\Pagination\LengthAwarePaginator<\App\Entities\Role>
   */
  function search($parameter, $search, $regex = true, $limit = null, $offset = 1)
  {
    return $this->roleRepository->paginate($limit, $offset)->find($parameter, $search, $regex);
  }
}