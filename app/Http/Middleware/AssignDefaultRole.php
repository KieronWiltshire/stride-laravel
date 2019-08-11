<?php

namespace App\Http\Middleware;

use Closure;
use Domain\Role\Exceptions\RoleNotFoundException;
use Domain\Role\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignDefaultRole
{
  /**
   * @var RoleService
   */
  private $roleService;

  /**
   * Create a new assign default role middleware instance.
   * .
   * @param RoleService $roleService
   */
  public function __construct(
    RoleService $roleService
  ) {
    $this->roleService = $roleService;
  }

  /**
   * Handle an incoming request.
   *
   * @param Request $request
   * @param Closure $next
   * @param  string|null  $guard
   * @return mixed
   */
  public function handle($request, Closure $next, $guard = null)
  {
    $user = request()->user();

    if ($user && $this->roleService->getRolesFromUser($user)->count() <= 0) {
      try {
        $defaultRole = $this->roleService->getDefaultRole();
        $this->roleService->addRoleToUser($user, $defaultRole, false);
      } catch (RoleNotFoundException $e) {
        // Do nothing if there is no default role configured
      }
    }

    return $next($request);
  }
}
