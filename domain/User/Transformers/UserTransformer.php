<?php

namespace Domain\User\Transformers;

use Domain\Permission\Transformers\PermissionTransformer;
use Domain\Role\Transformers\RoleTransformer;
use Domain\User\User;
use Illuminate\Support\Facades\Gate;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
  /**
   * @var \Domain\Role\Transformers\RoleTransformer
   */
  protected $roleTransformer;

  /**
   * @var \Domain\Permission\Transformers\PermissionTransformer
   */
  protected $permissionTransformer;

  /**
   * Create a new user transformer instance
   *
   * @param \Domain\Role\Transformers\RoleTransformer $roleTransformer
   * @param \Domain\Permission\Transformers\PermissionTransformer $permissionTransformer
   */
  public function __construct(
    RoleTransformer $roleTransformer,
    PermissionTransformer $permissionTransformer
  ) {
    $this->roleTransformer = $roleTransformer;
    $this->permissionTransformer = $permissionTransformer;
  }

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
   * A Fractal transformer.
   *
   * @return array
   */
  public function transform(User $user)
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
   * @return \League\Fractal\Resource\Item
   */
  public function includeRoles(User $user)
  {
    return $this->collection($user->roles, $this->roleTransformer, false);
  }

  /**
   * Include Permissions.
   *
   * @return \League\Fractal\Resource\Item
   */
  public function includePermissions(User $user)
  {
    return $this->collection($user->permissions, $this->permissionTransformer, false);
  }
}