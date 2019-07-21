<?php

namespace App\Transformers;

use App\Transformers\PermissionTransformer;
use App\Transformers\RoleTransformer;
use Domain\Permission\PermissionService;
use Domain\Role\RoleService;
use Illuminate\Support\Facades\Gate;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
  /**
   * List of resources possible to include
   *
   * @var array
   */
  protected $availableIncludes = [
    'roles',
    'permissions'
  ];

  /**
   * @var \Domain\Permission\PermissionService
   */
  protected $permissionService;

  /**
   * @var \Domain\Role\RoleService
   */
  protected $roleService;

  /**
   * @var \App\Transformers\RoleTransformer
   */
  protected $roleTransformer;

  /**
   * @var \App\Transformers\PermissionTransformer
   */
  protected $permissionTransformer;

  /**
   * Create a new user transformer instance
   *
   * @param \Domain\Permission\PermissionService $permissionService
   * @param \Domain\Role\RoleService $roleService
   * @param \App\Transformers\RoleTransformer $roleTransformer
   * @param \App\Transformers\PermissionTransformer $permissionTransformer
   */
  public function __construct(
    PermissionService $permissionService,
    RoleService $roleService,
    RoleTransformer $roleTransformer,
    PermissionTransformer $permissionTransformer
  ) {
    $this->permissionService = $permissionService;
    $this->roleService = $roleService;
    $this->roleTransformer = $roleTransformer;
    $this->permissionTransformer = $permissionTransformer;
  }

  /**
   * A Fractal transformer.
   *
   * @return array
   */
  public function transform($user)
  {
    $visible = [];

    $viewUserDetail = Gate::allows('user.view', $user);

    if ($viewUserDetail || request()->route()->hasParameter('email')) {
      $visible[] = 'email';
    }

    return $user->makeVisible($visible)->toArray();
  }

  /**
   * Include Roles.
   *
   * @return \League\Fractal\Resource\Collection
   */
  public function includeRoles($user)
  {
    return $this->collection($this->roleService->getRolesFromUser($user), $this->roleTransformer, false);
  }

  /**
   * Include Permissions.
   *
   * @return \League\Fractal\Resource\Collection
   */
  public function includePermissions($user)
  {
    return $this->collection($this->permissionService->getPermissionsFromUser($user), $this->permissionTransformer, false);
  }
}