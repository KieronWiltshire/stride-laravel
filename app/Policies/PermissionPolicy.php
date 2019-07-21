<?php

namespace App\Policies;

use Domain\User\User;
use Domain\Permission\Permission;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Policies\BasePolicy;

class PermissionPolicy extends BasePolicy
{
  use HandlesAuthorization;

  /**
   * Determine if the specified user can create a permission.
   *
   * @param \Domain\User\User|null $user
   * @return bool
   */
  public function create(?User $user)
  {
    return $this->fallbackToDefault($user, function($subject) use ($user) {
      return (
        $subject->hasPermission('permission.create')
      );
    });
  }

  /**
   * Determine if the given permission can be updated by the specified user.
   *
   * @param \Domain\User\User|null $user
   * @param \Domain\Permission\Permission $permission
   * @return bool
   */
  public function update(?User $user, Permission $permission)
  {
    return $this->fallbackToDefault($user, function($subject) use ($user, $permission) {
      return (
        $subject->hasPermission('permission.update.all')
      );
    });
  }

  /**
   * Determine if the specified user can assign the specified permission.
   *
   * @param \Domain\User\User|null $user
   * @param \Domain\Permission\Permission $permission
   * @return bool
   */
  public function assign(?User $user, Permission $permission)
  {
    return $this->fallbackToDefault($user, function($subject) use ($user, $permission) {
      return (
        $subject->hasPermission('permission.assign.all') || $subject->hasPermission('role.assign.' . $permission->getKeyName())
      );
    });
  }

  /**
   * Determine if the specified user can deny the specified permission.
   *
   * @param \Domain\User\User|null $user
   * @param \Domain\Permission\Permission $permission
   * @return bool
   */
  public function deny(?User $user, Permission $permission)
  {
    return $this->fallbackToDefault($user, function($subject) use ($user, $permission) {
      return (
        $subject->hasPermission('permission.deny.all') || $subject->hasPermission('role.deny.' . $permission->getKeyName())
      );
    });
  }
}
