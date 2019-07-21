<?php

namespace App\Transformers;

use App\Transformers\PermissionTransformer;
use Domain\Role\Role;
use Domain\Permission\PermissionService;
use League\Fractal\TransformerAbstract;

class RoleTransformer extends TransformerAbstract
{
  /**
   * List of resources possible to include
   *
   * @var array
   */
  protected $availableIncludes = [
    'permissions'
  ];

  /**
   * @var \Domain\Permission\PermissionService
   */
  protected $permissionService;

  /**
   * @var \App\Transformers\PermissionTransformer
   */
  protected $permissionTransformer;

  /**
   * Create a new role transformer instance
   *
   * @param \Domain\Permission\PermissionService $permission
   * @param \App\Transformers\PermissionTransformer $permissionTransformer
   */
  public function __construct(
    PermissionService $permissionService,
    PermissionTransformer $permissionTransformer
  ) {
    $this->roleService = $permissionService;
    $this->permissionTransformer = $permissionTransformer;
  }

  /**
   * A Fractal transformer.
   *
   * @return array
   */
  public function transform($role)
  {
    $visible = [];

    return $role->makeVisible($visible)->toArray();
  }

  /**
   * Include Permissions.
   *
   * @return \League\Fractal\Resource\Collection
   */
  public function includePermissions($role)
  {
    return $this->collection($this->permissionService->getPermissionsFromRole($role), $this->permissionTransformer, false);
  }
}