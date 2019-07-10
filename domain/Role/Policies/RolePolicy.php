<?php

namespace Domain\Role\Policies;

use Domain\User\User;
use Domain\Role\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
  use HandlesAuthorization;

  /**
   * Create a new policy instance.
   *
   * @return void
   */
  public function __construct()
  {
    //
  }

  /**
   * Determine if the specified user can create a role.
   *
   * @param \Domain\User\User $user
   * @return bool
   */
  public function create(User $user)
  {
    return (
      $user->laratrustCan('role.create')
    );
  }

  /**
   * Determine if the specified user can update the given role.
   *
   * @param \Domain\User\User $user
   * @param \Domain\Role\Role $role
   * @return bool
   */
  public function update(User $user, Role $role)
  {
    return (
      $user->laratrustCan('role.update.all')
    );
  }

  /**
   * Determine if the specified user can assign the specified role.
   *
   * @param \Domain\User\User $user
   * @param \Domain\Role\Role $role
   * @return bool
   */
  public function assign(User $user, Role $role)
  {
    return (
      $user->laratrustCan('role.assign.all') || $user->laratrustCan('role.assign.' . $role->getKeyName())
    );
  }

  /**
   * Determine if the specified user can deny the specified role.
   *
   * @param \Domain\User\User $user
   * @param \Domain\Role\Role $role
   * @return bool
   */
  public function deny(User $user, Role $role)
  {
    return (
      $user->laratrustCan('role.deny.all') || $user->laratrustCan('role.deny.' . $role->getKeyName())
    );
  }

  /**
   * Determine if the specified user can assign permissions to the given role.
   *
   * @param \Domain\User\User $user
   * @param \Domain\Role\Role $roleToAssign
   * @return bool
   */
  public function assignPermission(User $user, Role $roleToAssign)
  {
    return (
      $user->laratrustCan('role.assign-permission')
    );
  }

  /**
   * Determine if the specified user can deny permissions to the given role.
   *
   * @param \Domain\User\User $user
   * @param \Domain\Role\Role $roleToDeny
   * @return bool
   */
  public function denyPermission(User $user, Role $roleToDeny)
  {
    return (
      $user->laratrustCan('role.deny-permission')
    );
  }
}
