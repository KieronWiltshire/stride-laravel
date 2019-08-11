<?php

namespace App\Policies;

use Domain\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Policies\BasePolicy;

class UserPolicy extends BasePolicy
{
  use HandlesAuthorization;

  /**
   * Determine if the given user can view other user details such
   * as their email address.
   *
   * @param User|null $user
   * @param User $userToView
   * @return bool
   */
  public function view(?User $user, User $userToView)
  {
    return $this->fallbackToDefault($user, function($subject) use ($user, $userToView) {
      return (
        ($subject->hasPermission('user.view.me') && ($user && $user->id === $userToView->id))
        || ($subject->hasPermission('user.view.all'))
      );
    });
  }

  /**
   * Determine if the given user can be updated by the specified user.
   *
   * @param User|null $user
   * @param User $userToUpdate
   * @return bool
   */
  public function update(?User $user, User $userToUpdate)
  {
    return $this->fallbackToDefault($user, function($subject) use ($user, $userToUpdate) {
      return (
        ($subject->hasPermission('user.update.me') && ($user && $user->id === $userToUpdate->id))
        || ($subject->hasPermission('user.update.all'))
      );
    });
  }

  /**
   * Determine if the specified user can assign roles to the given user.
   *
   * @param User|null $user
   * @param User $userToAssign
   * @return bool
   */
  public function assignRole(?User $user, User $userToAssign)
  {
    return $this->fallbackToDefault($user, function($subject) use ($user, $userToAssign) {
      return (
        $subject->hasPermission('user.assign-role')
      );
    });
  }

  /**
   * Determine if the specified user can deny roles to the given user.
   *
   * @param User|null $user
   * @param User $userToDeny
   * @return bool
   */
  public function denyRole(?User $user, User $userToDeny)
  {
    return $this->fallbackToDefault($user, function($subject) use ($user, $userToDeny) {
      return (
        $subject->hasPermission('user.deny-role')
      );
    });
  }

  /**
   * Determine if the specified user can assign permissions to the given user.
   *
   * @param User|null $user
   * @param User $userToAssign
   * @return bool
   */
  public function assignPermission(?User $user, User $userToAssign)
  {
    return $this->fallbackToDefault($user, function($subject) use ($user, $userToAssign) {
      return (
        $subject->hasPermission('user.assign-permission')
      );
    });
  }

  /**
   * Determine if the specified user can deny permissions to the given user.
   *
   * @param User|null $user
   * @param User $userToDeny
   * @return bool
   */
  public function denyPermission(?User $user, User $userToDeny)
  {
    return $this->fallbackToDefault($user, function($subject) use ($user, $userToDeny) {
      return (
        $subject->laratrustCan('user.deny-permission')
      );
    });
  }
}
