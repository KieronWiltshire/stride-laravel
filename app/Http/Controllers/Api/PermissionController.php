<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Permission\PermissionNotFoundException;
use App\Http\Controllers\Controller;
use App\Contracts\Repositories\PermissionRepository;
use App\Exceptions\Http\BadRequestError;
use App\Transformers\PermissionTransformer;

class PermissionController extends Controller
{
  /**
   * @var \App\Contracts\Repositories\PermissionRepository
   */
  protected $permissionRepository;

  /**
   * @var \App\Transformers\PermissionTransformer
   */
  protected $permissionTransformer;

  /**
   * Create a new permission controller instance
   *
   * @param \App\Contracts\Repositories\PermissionRepository $permissionRepository
   * @param \App\Transformers\PermissionTransformer $permissionTransformer
   */
  public function __construct(
    PermissionRepository $permissionRepository,
    PermissionTransformer $permissionTransformer
  ) {
    $this->permissionRepository = $permissionRepository;
    $this->permissionTransformer = $permissionTransformer;
  }

  /**
   * Retrieve an index of permissions.
   *
   * @return \Illuminate\Pagination\LengthAwarePaginator<\App\Entities\Permission>
   *
   * @throws \App\Exceptions\Pagination\InvalidPaginationException
   */
  public function index()
  {
    $permissions = $this->permissionRepository->allAsPaginated(request()->query('limit'), request()->query('offset'))
      ->setPath(route('api.permission.index'))
      ->setPageName('offset')
      ->appends([
        'limit' => request()->query('limit')
      ]);

    return fractal($permissions, $this->permissionTransformer);
  }

  /**
   * Create a new permission.
   *
   * @return \App\Entities\Permission
   *
   * @throws \App\Exceptions\Permission\CannotCreatePermissionException
   */
  public function create()
  {
    $this->authorize('permission.create');

    $permission = $this->permissionRepository->create([
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
   * @return \App\Entities\Permission
   *
   * @throws \App\Exceptions\Permission\PermissionNotFoundException
   */
  public function getById($id)
  {
    try {
      return fractal($this->permissionRepository->findById($id), $this->permissionTransformer);
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
   * @return \App\Entities\Permission
   *
   * @throws \App\Exceptions\Permission\PermissionNotFoundException
   */
  public function getByName($name)
  {
    try {
      return fractal($this->permissionRepository->findByName($name), $this->permissionTransformer);
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
   * @return \Illuminate\Pagination\LengthAwarePaginator<\App\Entities\Permission>
   *
   * @throws \App\Exceptions\Http\BadRequestError
   * @throws \App\Exceptions\Pagination\InvalidPaginationException
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

    $permissions = $this->permissionRepository->findAsPaginated(request()->query('parameter'), request()->query('search'), (bool) request()->query('regex'), request()->query('limit'), request()->query('offset'))
      ->setPath(route('api.permission.search'))
      ->setPageName('offset')
      ->appends([
        'limit' => request()->query('limit')
      ]);

    return fractal($permissions, $this->permissionTransformer);
  }

  /**
   * Update a permission.
   *
   * @param integer $id
   * @return \App\Entities\Permission
   *
   * @throws \App\Exceptions\Permission\PermissionNotFoundException
   * @throws \App\Exceptions\Permission\CannotUpdatePermissionException
   */
  public function update($id)
  {
    try {
      $permission = $this->permissionRepository->findById($id);
      $this->authorize('permission.update', $permission);

      $permission = $this->permissionRepository->update($permission, [
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
