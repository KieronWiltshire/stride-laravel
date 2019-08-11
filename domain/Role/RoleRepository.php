<?php

namespace Domain\Role;

use Domain\Role\Contracts\Repositories\RoleRepository as RoleRepositoryInterface;
use Domain\Role\Events\RoleCreatedEvent;
use Domain\Role\Events\RoleUpdatedEvent;
use Domain\Role\Exceptions\CannotCreateRoleException;
use Domain\Role\Exceptions\CannotUpdateRoleException;
use Domain\Role\Exceptions\RoleNotFoundException;
use Domain\Role\Exceptions\UnableToSetDefaultRoleException;
use Domain\User\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Support\Repositories\AppRepository;
use Domain\Role\Validators\RoleCreateValidator;
use Domain\Role\Validators\RoleUpdateValidator;
use Exception;

class RoleRepository extends AppRepository implements RoleRepositoryInterface
{
  /**
   * @var RoleCreateValidator
   */
  protected $roleCreateValidator;

  /**
   * @var RoleUpdateValidator
   */
  protected $roleUpdateValidator;

  /**
   * Create a new role repository instance.
   *
   * @param RoleCreateValidator $roleCreateValidator
   * @param RoleUpdateValidator $roleUpdateValidator
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
   * @return Role
   *
   * @throws CannotCreateRoleException
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
   * @return Role
   *
   * @throws CannotCreateRoleException
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
   * @return Role
   *
   * @throws RoleNotFoundException
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
   * @return Role
   *
   * @throws RoleNotFoundException
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
   * Set the specified role as the default role.
   *
   * @param Role $role
   * @return void
   *
   * @throws UnableToSetDefaultRoleException
   */
  public function setDefaultRole(Role $role)
  {
    try {
      DB::transaction(function () use ($role) {
        DB::table('roles')->update(['is_default' => false]);
        DB::table('roles')->where('id', $role->id)->update(['is_default' => true]);
      });
    } catch (Exception $e) {
      throw new UnableToSetDefaultRoleException();
    }
  }

  /**
   * Retrieve the default role.
   *
   * @return Role
   *
   * @throws RoleNotFoundException
   */
  public function getDefaultRole()
  {
    $role = $this->execute(Role::where('is_default', true), true);

    if (!$role) {
      throw new RoleNotFoundException();
    }

    return $role;
  }

  /**
   * Update a role.
   *
   * @param Role $role
   * @param array $attributes
   * @return Role
   *
   * @throws CannotUpdateRoleException
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
   * @param User $user
   * @param Role $role
   * @param boolean $persist
   * @return User
   */
  public function addRoleToUser(User $user, Role $role, $persist = true)
  {
    return $this->addRolesToUser($user, [
      $role
    ], $persist);
  }

  /**
   * Add roles to the user.
   *
   * @param User $user
   * @param Collection|array $roles
   * @param boolean $persist
   * @return User
   */
  public function addRolesToUser(User $user, $roles = [], $persist = true)
  {
    if ($persist) {
      return $user->attachRoles($roles);
    } else {
      foreach ($roles as $role) {
        if (!$user->hasRole($role)) {
          $user->roles->push($role);
        }
      }
      return $user;
    }
  }

  /**
   * Remove a role from the user.
   *
   * @param User $user
   * @param Role $role
   * @param boolean $persist
   * @return User
   */
  public function removeRoleFromUser(User $user, Role $role, $persist = true)
  {
    return $this->removeRolesFromUser($user, [
      $role
    ], $persist);
  }

  /**
   * Remove roles from the user.
   *
   * @param User $user
   * @param Collection|array $roles
   * @param boolean $persist
   * @return User
   */
  public function removeRolesFromUser(User $user, $roles = [], $persist = true)
  {
    if ($persist) {
      return $user->detachRoles($roles);
    } else {
      return $user->setRelation('roles', $user->roles->whereNotIn('id', (($roles instanceof Collection) ? $roles : collect($roles))->only('id')));
    }
  }

  /**
   * Set all of the roles of the specified user.
   *
   * @param User $user
   * @param Collection|array $roles
   * @param boolean $persist
   * @return User
   */
  public function setUserRoles(User $user, $roles = [], $persist = true)
  {
    if ($persist) {
      return $user->syncRoles($roles);
    } else {
      return $user->setRelation('roles', ($roles instanceof Collection) ? $roles : collect($roles));
    }
  }

  /**
   * Retrieve all of the roles for the specified user.
   *
   * @param User $user
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Role\Role>
   */
  public function getRolesForUser(User $user)
  {
    return $user->roles;
  }

  /**
   * Retrieve all of the users that are associated with the specified role.
   *
   * @param Role $role
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\User\User>
   */
  public function getUsersWithRole(Role $role)
  {
    return $this->getUsersWithRoles([$role]);
  }

  /**
   * Retrieve all of the users that are associated with any of the specified roles.
   *
   * @param Collection|array $roles
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
