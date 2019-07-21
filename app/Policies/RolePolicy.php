<?php

namespace App\Policies;

use Domain\User\User;
use Domain\Role\Role;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Policies\BasePolicy;

class RolePolicy extends BasePolicy
{
  use HandlesAuthorization;

  /**
   * Determine if the specified user can create a role.
   *
   * @param \Domain\User\User|null $user
   * @return bool
   */
  public function create(?User $user)
  {
    return $this->fallbackToDefault($user, function($subject) use ($user) {
      return (
        $subject->hasPermission('role.create')
      );
    });
  }

  /**
   * Determine if the specified user can update the given role.
   *
   * @param \Domain\User\User|null $user
   * @param \Domain\Role\Role $role
   * @return bool
   */
  public function update(?User $user, Role $role)
  {
    return $this->fallbackToDefault($user, function($subject) use ($user, $role) {
      return (
        $subject->hasPermission('role.update.all')
      );
    });
  }

  /**
   * Determine if the specified user can assign the specified role.
   *
   * @param \Domain\User\User|null $user
   * @param \Domain\Role\Role $role
   * @return bool
   */
  public function assign(?User $user, Role $role)
  {
    return $this->fallbackToDefault($user, function($subject) use ($user, $role) {
      return (
        $subject->hasPermission('role.assign.all') || $subject->hasPermission('role.assign.' . $role->getKeyName())
      );
    });
  }

  /**
   * Determine if the specified user can deny the specified role.
   *
   * @param \Domain\User\User|null $user
   * @param \Domain\Role\Role $role
   * @return bool
   */
  public function deny(?User $user, Role $role)
  {
    return $this->fallbackToDefault($user, function($subject) use ($user, $role) {
      return (
        $subject->hasPermission('role.deny.all') || $subject->hasPermission('role.deny.' . $role->getKeyName())
      );
    });
  }

  /**
   * Determine if the specified user can assign permissions to the given role.
   *
   * @param \Domain\User\User|null $user
   * @param \Domain\Role\Role $role
   * @return bool
   */
  public function assignPermission(?User $user, Role $role)
  {
    return $this->fallbackToDefault($user, function($subject) use ($user, $role) {
      return (
        $subject->hasPermission('role.assign-permission')
      );
    });
  }

  /**
   * Determine if the specified user can deny permissions to the given role.
   *
   * @param \Domain\User\User|null $user
   * @param \Domain\Role\Role $role
   * @return bool
   */
  public function denyPermission(?User $user, Role $role)
  {
    return $this->fallbackToDefault($user, function($subject) use ($user, $role) {
      return (
        $subject->hasPermission('role.deny-permission')
      );
    });
  }
}
