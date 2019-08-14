<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Domain\Permission\Exceptions\PermissionAssignedException;
use Domain\Permission\Exceptions\PermissionNotAssignedException;
use Domain\Permission\Exceptions\PermissionNotFoundException;
use Domain\Permission\PermissionService;
use App\Transformers\PermissionTransformer;
use Domain\Role\Exceptions\CannotCreateRoleException;
use Domain\Role\Exceptions\CannotUpdateRoleException;
use Domain\Role\Exceptions\RoleNotFoundException;
use Domain\Role\Role;
use Domain\Role\RoleService;
use App\Transformers\RoleTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Support\Exceptions\Http\BadRequestError;
use Support\Exceptions\Pagination\InvalidPaginationException;
use Support\Serializers\Fractal\OptionalDataKeySerializer;

class RoleController extends Controller
{
    /**
     * @var RoleService
     */
    protected $roleService;

    /**
     * @var RoleTransformer
     */
    protected $roleTransformer;

    /**
     * @var PermissionService
     */
    protected $permissionService;

    /**
     * @var PermissionTransformer
     */
    protected $permissionTransformer;

    /**
     * @var OptionalDataKeySerializer
     */
    protected $noDataKeySerializer;

    /**
     * Create a new role controller instance
     *
     * @param RoleService $roleService
     * @param RoleTransformer $roleTransformer
     * @param PermissionService $permissionService
     * @param PermissionTransformer %permissionTransformer
     */
    public function __construct(
      RoleService $roleService,
      RoleTransformer $roleTransformer,
      PermissionService $permissionService,
      PermissionTransformer $permissionTransformer
  ) {
        $this->roleService = $roleService;
        $this->roleTransformer = $roleTransformer;
        $this->permissionService = $permissionService;
        $this->permissionTransformer = $permissionTransformer;
        $this->noDataKeySerializer = new OptionalDataKeySerializer(false);
    }

    /**
     * Retrieve an index of roles.
     *
     * @return LengthAwarePaginator<\Domain\Role\Role>
     *
     * @throws InvalidPaginationException
     */
    public function index()
    {
        $roles = $this->roleService->index(request()->query('limit'), request()->query('offset'))
      ->setPath(route('api.role.index'));

        return fractal($roles, $this->roleTransformer)->parseIncludes(['permissions']);
    }

    /**
     * Create a new role.
     *
     * @return Role
     *
     * @throws CannotCreateRoleException
     */
    public function create()
    {
        $this->authorize('role.create');

        $role = $this->roleService->create([
      'name' => request()->input('name'),
      'display_name' => request()->input('display_name'),
      'description' => request()->input('description')
    ]);

        return response([], 201)
      ->header('Location', route('api.role.get', $role->id));
    }

    /**
     * Retrieve a role by id.
     *
     * @param integer $id
     * @return Role
     *
     * @throws RoleNotFoundException
     */
    public function getById($id)
    {
        try {
            return fractal($this->roleService->findById($id), $this->roleTransformer)->parseIncludes(['permissions']);
        } catch (RoleNotFoundException $e) {
            throw $e->setContext([
        'id' => [
          __('role.id.not_found')
        ]
      ]);
        }
    }

    /**
     * Retrieve a role by name.
     *
     * @param string $name
     * @return Role
     *
     * @throws RoleNotFoundException
     */
    public function getByName($name)
    {
        try {
            return fractal($this->roleService->findByName($name), $this->roleTransformer)->parseIncludes(['permissions']);
        } catch (RoleNotFoundException $e) {
            throw $e->setContext([
        'id' => [
          __('role.name.not_found')
        ]
      ]);
        }
    }

    /**
     * Retrieve an index of roles matching a particular search phrase.
     *
     * @return LengthAwarePaginator<\Domain\Role\Role>
     *
     * @throws BadRequestError
     * @throws InvalidPaginationException
     */
    public function search()
    {
        switch (strtolower(request()->query('parameter'))) {
      case 'name':
      case 'display_name':
        break;
      default:
        throw (new BadRequestError())->setContext([
          'parameter' => [
            __('validation.regex', ['attribute' => 'parameter'])
          ]
        ]);
    }

        $roles = $this->roleService->search(
        request()->query('parameter'),
        request()->query('search'),
        (bool) request()->query('regex'),
        request()->query('limit'),
        request()->query('offset')
    )->setPath(route('api.role.search'));

        return fractal($roles, $this->roleTransformer)->parseIncludes(['permissions']);
    }

    /**
     * Update a role.
     *
     * @param integer $id
     * @return Role
     *
     * @throws RoleNotFoundException
     * @throws CannotUpdateRoleException
     */
    public function update($id)
    {
        try {
            $role = $this->roleService->findById($id);
            $this->authorize('role.update', $role);

            $role = $this->roleService->update($role, [
        'name' => request()->input('name'),
        'display_name' => request()->input('display_name'),
        'description' => request()->input('description')
      ]);

            return fractal($role, $this->roleTransformer)->parseIncludes(['permissions']);
        } catch (RoleNotFoundException $e) {
            throw $e->setContext([
        'id' => [
          __('role.id.not_found')
        ]
      ]);
        }
    }

    /**
     * Add the specified permission to the specified role.
     *
     * @param $id
     * @param $permissionId
     * @return JsonResponse
     */
    public function assignPermission($id, $permissionId)
    {
        try {
            $role = $this->roleService->findById($id);
            $permission = $this->permissionService->findById($permissionId);

            $this->authorize('role.assign-permission', $role);
            $this->authorize('permission.assign', $permission);

            if ($this->permissionService->roleHasPermission($role, $permission)) {
                throw new PermissionAssignedException();
            }

            $this->permissionService->addPermissionToRole($role, $permission);

            return response([
        'message' => __('role.permission.assigned'),
        'data' => fractal($role, $this->roleTransformer, $this->noDataKeySerializer)->parseIncludes(['permissions'])
      ], 200);
        } catch (PermissionNotFoundException $e) {
            throw $e->setContext([
        'permissionId' => [
          __('permission.id.not_found')
        ]
      ]);
        } catch (RoleNotFoundException $e) {
            throw $e->setContext([
        'id' => [
          __('role.id.not_found')
        ]
      ]);
        }
    }

    /**
     * Add the specified permissions to the specified role.
     *
     * @param $id
     * @return JsonResponse
     */
    public function assignPermissions($id)
    {
        try {
            $role = $this->roleService->findById($id);
            $permissions = $this->permissionService->find('id', request()->input('permissionIds'));

            if ($permissions->count() != (is_array($permissions) ? count(request()->input('permissionIds')) : 1)) {
                throw new PermissionNotFoundException();
            }

            $this->authorize('role.assign-permission', $role);

            foreach ($permissions as $permission) {
                $this->authorize('permission.assign', $permission);
            }

            if ($this->permissionService->roleHasPermissions($role, $permissions)) {
                throw new PermissionAssignedException();
            }

            $this->permissionService->addPermissionsToRole($role, $permissions);

            return response([
        'message' => __('role.permission.assigned'),
        'data' => fractal($role, $this->roleTransformer, $this->noDataKeySerializer)->parseIncludes(['permissions'])
      ], 200);
        } catch (PermissionNotFoundException $e) {
            throw $e->setContext([
        'permissionId' => [
          __('permission.id.not_found')
        ]
      ]);
        } catch (RoleNotFoundException $e) {
            throw $e->setContext([
        'id' => [
          __('role.id.not_found')
        ]
      ]);
        }
    }

    /**
     * Remove the specified permission from the specified role.
     *
     * @param $id
     * @param $permissionId
     * @return JsonResponse
     */
    public function denyPermission($id, $permissionId)
    {
        try {
            $role = $this->roleService->findById($id);
            $permission = $this->permissionService->findById($permissionId);

            $this->authorize('role.deny-permission', $role);
            $this->authorize('permission.deny', $permission);

            if (!$this->permissionService->roleHasPermission($role, $permission)) {
                throw new PermissionNotAssignedException();
            }

            $this->permissionService->removePermissionFromRole($role, $permission);

            return response([
        'message' => __('role.permission.denied'),
        'data' => fractal($role, $this->roleTransformer, $this->noDataKeySerializer)->parseIncludes(['permissions'])
      ], 200);
        } catch (PermissionNotFoundException $e) {
            throw $e->setContext([
        'permissionId' => [
          __('permission.id.not_found')
        ]
      ]);
        } catch (RoleNotFoundException $e) {
            throw $e->setContext([
        'id' => [
          __('role.id.not_found')
        ]
      ]);
        }
    }

    /**
     * Remove the specified permissions from the specified role.
     *
     * @param $id
     * @return JsonResponse
     */
    public function denyPermissions($id)
    {
        try {
            $role = $this->roleService->findById($id);
            $permissions = $this->permissionService->find('id', request()->input('permissionIds'));

            if ($permissions->count() != (is_array($permissions) ? count(request()->input('permissionIds')) : 1)) {
                throw new PermissionNotFoundException();
            }

            $this->authorize('role.deny-permission', $role);

            foreach ($permissions as $permission) {
                $this->authorize('permission.deny', $permission);
            }

            if (!$this->permissionService->roleHasPermissions($role, $permissions)) {
                throw new PermissionNotAssignedException();
            }

            $this->permissionService->removePermissionsFromRole($role, $permissions);

            return response([
        'message' => __('role.permission.denied'),
        'data' => fractal($role, $this->roleTransformer, $this->noDataKeySerializer)->parseIncludes(['permissions'])
      ], 200);
        } catch (PermissionNotFoundException $e) {
            throw $e->setContext([
        'permissionId' => [
          __('permission.id.not_found')
        ]
      ]);
        } catch (RoleNotFoundException $e) {
            throw $e->setContext([
        'id' => [
          __('role.id.not_found')
        ]
      ]);
        }
    }
}
