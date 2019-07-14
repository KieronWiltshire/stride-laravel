<?php

namespace Domain\Role\Transformers;

use Domain\Permission\Transformers\PermissionTransformer;
use Domain\Role\Role;
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
   * @var \Domain\Permission\Transformers\PermissionTransformer
   */
  protected $permissionTransformer;

  /**
   * Create a new role transformer instance
   *
   * @param \Domain\Permission\Transformers\PermissionTransformer $permissionTransformer
   */
  public function __construct(
    PermissionTransformer $permissionTransformer
  ) {
    $this->permissionTransformer = $permissionTransformer;
  }

  /**
   * A Fractal transformer.
   *
   * @return array
   */
  public function transform(Role $role)
  {
    $visible = [];

    return $role->makeVisible($visible)->toArray();
  }

  /**
   * Include Permissions.
   *
   * @return \League\Fractal\Resource\Collection
   */
  public function includePermissions(Role $role)
  {
    return $this->collection($role->permissions, $this->permissionTransformer, false);
  }
}