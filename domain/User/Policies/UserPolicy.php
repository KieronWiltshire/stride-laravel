<?php

namespace Domain\User\Policies;

use Domain\User\User;
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
  }

  /**
   * Determine if the given user can view other user details such
   * as their email address.
   *
   * @param \Domain\User\User $user
   * @param \Domain\User\User $userToView
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
   * @param \Domain\User\User $user
   * @param \Domain\User\User $userToUpdate
   * @return bool
   */
  public function update(User $user, User $userToUpdate)
  {
    return (
      ($user->laratrustCan('user.update.me') && $user->id === $userToUpdate->id)
      || ($user->laratrustCan('user.update.all'))
    );
  }

  /**
   * Determine if the specified user can assign roles to the given user.
   *
   * @param \Domain\User\User $user
   * @param \Domain\User\User $userToAssign
   * @return bool
   */
  public function assignRole(User $user, User $userToAssign)
  {
    return (
      $user->laratrustCan('user.assign-role')
    );
  }

  /**
   * Determine if the specified user can deny roles to the given user.
   *
   * @param \Domain\User\User $user
   * @param \Domain\User\User $userToDeny
   * @return bool
   */
  public function denyRole(User $user, User $userToDeny)
  {
    return (
      $user->laratrustCan('user.deny-role')
    );
  }

  /**
   * Determine if the specified user can assign permissions to the given user.
   *
   * @param \Domain\User\User $user
   * @param \Domain\User\User $userToAssign
   * @return bool
   */
  public function assignPermission(User $user, User $userToAssign)
  {
    return (
      $user->laratrustCan('user.assign-permission')
    );
  }

  /**
   * Determine if the specified user can deny permissions to the given user.
   *
   * @param \Domain\User\User $user
   * @param \Domain\User\User $userToDeny
   * @return bool
   */
  public function denyPermission(User $user, User $userToDeny)
  {
    return (
      $user->laratrustCan('user.deny-permission')
    );
  }
}
