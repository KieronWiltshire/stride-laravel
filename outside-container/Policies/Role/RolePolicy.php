<?php

namespace App\Policies\Role;

use App\Entities\User;
use App\Entities\Role;
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
   * @param \App\Entities\User $user
   * @return bool
   */
  public function create(User $user)
  {
    return (
      $user->laratrustCan('role.create')
    );
  }

  /**
   * Determine if the given role can be updated by the specified user.
   *
   * @param \App\Entities\User $user
   * @param \App\Entities\Role $role
   * @return bool
   */
  public function update(User $user, Role $role)
  {
    return (
      $user->laratrustCan('role.update.all')
    );
  }
}
