<?php

namespace Domain\Role;

use Domain\Role\Contracts\Repositories\RoleRepository as RoleRepositoryInterface;
use Domain\Permission\Permission;
use Domain\Role\Events\RoleCreatedEvent;
use Domain\Role\Events\RoleUpdatedEvent;
use Domain\Role\Exceptions\RoleNotFoundException;
use Domain\User\User;
use Infrastructure\Repositories\AppRepository;
use Domain\Role\Validators\RoleCreateValidator;
use Domain\Role\Validators\RoleUpdateValidator;
use Exception;
use Laratrust\Traits\LaratrustUserTrait;

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
  public function all()
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
  public function create($attributes)
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
  public function find($parameter, $search, $regex = true)
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
  public function findById($id)
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
  public function findByName($name)
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
  public function update(Role $role, $attributes)
  {
    $this->roleUpdateValidator->validate($attributes);

    // TODO:

    if ($role->save()) {
      event(new RoleUpdatedEvent($role, $attributes));

      return $role;
    }
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
    return $this->addRolesToUser($user, [
      $role
    ]);
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
    return $user->attachRoles($roles);
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
    return $this->removeRolesFromUser($user, [
      $role
    ]);
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
    return $user->detachRoles($roles);
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
    return $user->syncRoles($roles);
  }

  /**
   * Retrieve all of the roles for the specified user.
   *
   * @param \Domain\User\User $user
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Role\Role>
   */
  public function getRolesFromUser(User $user)
  {
    return $user->roles;
  }

  /**
   * Retrieve all of the users that are associated with the specified role.
   *
   * @param \Domain\Role\Role $role
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\User\User>
   */
  public function getUsersWithRole(Role $role)
  {
    return $this->getUsersWithRoles([$role]);
  }

  /**
   * Retrieve all of the users that are associated with any of the specified roles.
   *
   * @param \Illuminate\Support\Collection|array $roles
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\User\User>
   */
  public function getUsersWithRoles($roles = [])
  {
    $query = User::query();

    foreach ($roles as $index => $role) {
      if ($index <= 0) {
        $query->whereRoleIs($role->name);
      } else {
        $query->orWhereRoleIs($role->name);
      }
    }

    return $this->execute($query);
  }
}
