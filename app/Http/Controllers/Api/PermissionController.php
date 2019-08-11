<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Domain\Permission\Exceptions\CannotCreatePermissionException;
use Domain\Permission\Exceptions\CannotUpdatePermissionException;
use Domain\Permission\Exceptions\PermissionNotFoundException;
use Domain\Permission\Permission;
use Domain\Permission\PermissionService;
use App\Transformers\PermissionTransformer;
use Illuminate\Pagination\LengthAwarePaginator;
use Support\Exceptions\Http\BadRequestError;
use Support\Exceptions\Pagination\InvalidPaginationException;

class PermissionController extends Controller
{
  /**
   * @var PermissionService
   */
  protected $permissionService;

  /**
   * @var PermissionTransformer
   */
  protected $permissionTransformer;

  /**
   * Create a new permission controller instance
   *
   * @param PermissionService $permissionService
   * @param PermissionTransformer $permissionTransformer
   */
  public function __construct(
    PermissionService $permissionService,
    PermissionTransformer $permissionTransformer
  ) {
    $this->permissionService = $permissionService;
    $this->permissionTransformer = $permissionTransformer;
  }

  /**
   * Retrieve an index of permissions.
   *
   * @return LengthAwarePaginator<\Domain\Permission\Permission>
   *
   * @throws InvalidPaginationException
   */
  public function index()
  {
    $permissions = $this->permissionService->index(request()->query('limit'), request()->query('offset'))
      ->setPath(route('api.permission.index'));

    return fractal($permissions, $this->permissionTransformer);
  }

  /**
   * Create a new permission.
   *
   * @return Permission
   *
   * @throws CannotCreatePermissionException
   */
  public function create()
  {
    $this->authorize('permission.create');

    $permission = $this->permissionService->create([
      'name' => request()->input('name'),
      'display_name' => request()->input('display_name'),
      'description' => request()->input('description')
    ]);

    return response([], 201)
      ->header('Location', route('api.permission.get', $permission->id));
  }

  /**
   * Retrieve a permission by id.
   *
   * @param integer $id
   * @return Permission
   *
   * @throws PermissionNotFoundException
   */
  public function getById($id)
  {
    try {
      return fractal($this->permissionService->findById($id), $this->permissionTransformer);
    } catch (PermissionNotFoundException $e) {
      throw $e->setContext([
        'id' => [
          __('permission.id.not_found')
        ]
      ]);
    }
  }

  /**
   * Retrieve a permission by name.
   *
   * @param string $name
   * @return Permission
   *
   * @throws PermissionNotFoundException
   */
  public function getByName($name)
  {
    try {
      return fractal($this->permissionService->findByName($name), $this->permissionTransformer);
    } catch (PermissionNotFoundException $e) {
      throw $e->setContext([
        'id' => [
          __('permission.name.not_found')
        ]
      ]);
    }
  }

  /**
   * Retrieve an index of permissions matching a particular search phrase.
   *
   * @return LengthAwarePaginator<\Domain\Permission\Permission>
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

    $permissions = $this->permissionService->search(
      request()->query('parameter'),
      request()->query('search'),
      (bool) request()->query('regex'),
      request()->query('limit'),
      request()->query('offset')
    )->setPath(route('api.permission.search'));

    return fractal($permissions, $this->permissionTransformer);
  }

  /**
   * Update a permission.
   *
   * @param integer $id
   * @return Permission
   *
   * @throws PermissionNotFoundException
   * @throws CannotUpdatePermissionException
   */
  public function update($id)
  {
    try {
      $permission = $this->permissionService->findById($id);
      $this->authorize('permission.update', $permission);

      $permission = $this->permissionService->update($permission, [
        'name' => request()->input('name'),
        'display_name' => request()->input('display_name'),
        'description' => request()->input('description')
      ]);

      return fractal($permission, $this->permissionTransformer);
    } catch (PermissionNotFoundException $e) {
      throw $e->setContext([
        'id' => [
          __('permission.id.not_found')
        ]
      ]);
    }
  }
}
