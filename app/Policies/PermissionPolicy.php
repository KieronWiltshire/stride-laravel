<?php

namespace App\Policies;

use App\Entities\User;
use App\Entities\Permission;
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
    //
  }

  /**
   * Determine if the specified user can create a permission.
   *
   * @param \App\Entities\User $user
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
   * @param \App\Entities\User $user
   * @param \App\Entities\Permission $permission
   * @return bool
   */
  public function update(User $user, Permission $permission)
  {
    return (
      $user->laratrustCan('permission.update.all')
    );
  }
}
