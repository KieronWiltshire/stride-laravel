<?php

namespace Domain\Permission\Policies;

use Domain\User\User;
use Domain\Permission\Permission;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermissionPolicy
{
  use HandlesAuthorization;

  /**
   * Create a new policy instance.
   *
   * @return void
   */
  public function __construct()
  {
  }

  /**
   * Determine if the specified user can create a permission.
   *
   * @param \Domain\User\User $user
   * @return bool
   */
  public function create(User $user)
  {
    return (
      $user->laratrustCan('permission.create')
    );
  }

  /**
   * Determine if the given permission can be updated by the specified user.
   *
   * @param \Domain\User\User $user
   * @param \Domain\Permission\Permission $permission
   * @return bool
   */
  public function update(User $user, Permission $permission)
  {
    return (
      $user->laratrustCan('permission.update.all')
    );
  }

  /**
   * Determine if the specified user can assign the specified permission.
   *
   * @param \Domain\User\User $user
   * @param \Domain\Permission\Permission $permission
   * @return bool
   */
  public function assign(User $user, Permission $permission)
  {
    return (
      $user->laratrustCan('permission.assign.all') || $user->laratrustCan('role.assign.' . $permission->getKeyName())
    );
  }

  /**
   * Determine if the specified user can deny the specified permission.
   *
   * @param \Domain\User\User $user
   * @param \Domain\Permission\Permission $permission
   * @return bool
   */
  public function deny(User $user, Permission $permission)
  {
    return (
      $user->laratrustCan('permission.deny.all') || $user->laratrustCan('role.deny.' . $permission->getKeyName())
    );
  }
}
