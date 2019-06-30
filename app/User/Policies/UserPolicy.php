<?php

namespace App\User\Policies;

use App\Entities\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
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
   * Determine if the given user can view other user details such
   * as their email address.
   *
   * @param \App\Entities\User $user
   * @param \App\Entities\User $userToView
   * @return bool
   */
  public function view(?User $user, User $userToView)
  {
    return ($user) ? (
      ($user->laratrustCan('user.view.me') && $user->id === $userToView->id)
      || ($user->laratrustCan('user.view.all'))
    ) : false;
  }

  /**
   * Determine if the given user can be updated by the specified user.
   *
   * @param \App\Entities\User $user
   * @param \App\Entities\User $userToUpdate
   * @return bool
   */
  public function update(User $user, User $userToUpdate)
  {
    return (
      ($user->laratrustCan('user.update.me') && $user->id === $userToUpdate->id)
      || ($user->laratrustCan('user.update.all'))
    );
  }
}
