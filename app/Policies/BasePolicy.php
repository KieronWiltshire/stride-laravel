<?php

namespace App\Policies;

use Domain\Role\Exceptions\RoleNotFoundException;
use Domain\Role\RoleService;
use Domain\User\User;

class BasePolicy
{
  /**
   * @var \Domain\Role\RoleService
   */
  protected $roleService;

  /**
   * @var \Domain\Role\Role
   */
  protected $defaultRole;

  /**
   * Create a new app policy instance.
   *
   * @param RoleService $roleService
   */
  public function __construct(
    RoleService $roleService
  ) {
    $this->roleService = $roleService;
  }

  /**
   * Retrieve the default role as the subject if the user is null.
   *
   * @param \Domain\User\User|null $user
   * @return bool|\Domain\Role\Role|\Domain\User\User
   */
  public function fallbackToDefault(?User $user, \Closure $closure)
  {
    $subject = null;

    if ($user) {
      $subject = $user;
    } else if ($this->defaultRole) {
      $subject = $this->defaultRole;
    } else {
      try {
        $subject = $this->defaultRole = $this->roleService->getDefaultRole();
      } catch (RoleNotFoundException $e) {
        // Do nothing if there is no default role configured
      }
    }

    return ($subject) ? $closure->call($this, $subject) : false;
  }
}