<?php

namespace App\Transformers;

use App\Transformers\PermissionTransformer;
use App\Transformers\RoleTransformer;
use Domain\Permission\PermissionService;
use Domain\Role\RoleService;
use Illuminate\Support\Facades\Gate;
use League\Fractal\Resource\Collection;
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
     * @var PermissionService
     */
    protected $permissionService;

    /**
     * @var RoleService
     */
    protected $roleService;

    /**
     * @var RoleTransformer
     */
    protected $roleTransformer;

    /**
     * @var PermissionTransformer
     */
    protected $permissionTransformer;

    /**
     * Create a new user transformer instance
     *
     * @param PermissionService $permissionService
     * @param RoleService $roleService
     * @param RoleTransformer $roleTransformer
     * @param PermissionTransformer $permissionTransformer
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
     * @return Collection
     */
    public function includeRoles($user)
    {
        return $this->collection($this->roleService->getRolesForUser($user), $this->roleTransformer, false);
    }

    /**
     * Include Permissions.
     *
     * @return Collection
     */
    public function includePermissions($user)
    {
        return $this->collection($this->permissionService->getPermissionsForUser($user), $this->permissionTransformer, false);
    }
}
