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
   * The before method is called by the Gate and acts as a
   * policy filter. It will be called before each policy method.
   *
   * @param $user
   * @param $ability
   * @return bool|null
   */
  public function before($user, $ability)
  {
    $result = $this->fallbackToDefault($user, function($subject) use ($ability) {
      $module = explode('.', $ability)[0];

      if ($subject->hasPermission($module . '.*')) {
        return true;
      }
    });

    return ($result) ? $result : null;
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