<?php

namespace App\Contracts\Repositories;

use App\Entities\Role;

interface RoleRepository
{
  /**
   * Retrieve all of the roles.
   *
   * @return \Illuminate\Database\Eloquent\Collection<\App\Entities\Role>
   */
  function all();

  /**
   * Retrieve all of the roles.
   *
   * @param integer $limit
   * @param integer $offset
   * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<\App\Entities\Role>
   *
   * @throws \App\Exceptions\Pagination\InvalidPaginationException
   */
  function allAsPaginated($limit = null, $offset = 1);

  /**
   * Create a new role.
   *
   * @param array $attributes
   * @return \App\Entities\Role
   *
   * @throws \App\Exceptions\Role\CannotCreateRoleException
   */
  function create($attributes);

  /**
   * Find a role by an unknown parameter.
   *
   * @param number|string $parameter
   * @param number|string $search
   * @param boolean $regex
   * @return \Illuminate\Database\Eloquent\Collection<\App\Entities\Role>
   */
  function find($parameter, $search, $regex = true);

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
  function findAsPaginated($parameter, $search, $regex = true, $limit = null, $offset = 1);

  /**
   * Find a role by identifier.
   *
   * @param string $id
   * @return \App\Entities\Role
   *
   * @throws \App\Exceptions\Role\RoleNotFoundException
   */
  function findById($id);

  /**
   * Find a role by name.
   *
   * @param string $name
   * @return \App\Entities\Role
   *
   * @throws \App\Exceptions\Role\RoleNotFoundException
   */
  function findByName($name);

  /**
   * Update a role.
   *
   * @param \App\Entities\Role $role
   * @param array $attributes
   * @return \App\Entities\Role
   *
   * @throws \App\Exceptions\Role\CannotUpdateRoleException
   */
  function update(Role $role, $attributes);
}
