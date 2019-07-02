<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Domain\Role\Exceptions\RoleNotFoundException;
use Domain\Role\RoleService;
use Domain\Role\Transformers\RoleTransformer;
use Infrastructure\Exceptions\Http\BadRequestError;

class RoleController extends Controller
{
  /**
   * @var \Domain\Role\RoleService
   */
  protected $roleService;

  /**
   * @var \Domain\Role\Transformers\RoleTransformer
   */
  protected $roleTransformer;

  /**
   * Create a new role controller instance
   *
   * @param \Domain\Role\RoleService $roleService
   * @param \Domain\Role\Transformers\RoleTransformer $roleTransformer
   */
  public function __construct(
    RoleService $roleService,
    RoleTransformer $roleTransformer
  ) {
    $this->roleService = $roleService;
    $this->roleTransformer = $roleTransformer;
  }

  /**
   * Retrieve an index of roles.
   *
   * @return \Illuminate\Pagination\LengthAwarePaginator<\Domain\Role\Role>
   *
   * @throws \Infrastructure\Exceptions\Pagination\InvalidPaginationException
   */
  public function index()
  {
    $roles = $this->roleService->index(request()->query('limit'), request()->query('offset'))
      ->setPath(route('api.role.index'));

    return fractal($roles, $this->roleTransformer);
  }

  /**
   * Create a new role.
   *
   * @return \Domain\Role\Role
   *
   * @throws \Domain\Role\Exceptions\CannotCreateRoleException
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
   * @return \Domain\Role\Role
   *
   * @throws \Domain\Role\Exceptions\RoleNotFoundException
   */
  public function getById($id)
  {
    try {
      return fractal($this->roleService->findById($id), $this->roleTransformer);
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
   * @return \Domain\Role\Role
   *
   * @throws \Domain\Role\Exceptions\RoleNotFoundException
   */
  public function getByName($name)
  {
    try {
      return fractal($this->roleService->findByName($name), $this->roleTransformer);
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
   * @return \Illuminate\Pagination\LengthAwarePaginator<\Domain\Role\Role>
   *
   * @throws \Infrastructure\Exceptions\Http\BadRequestError
   * @throws \Infrastructure\Exceptions\Pagination\InvalidPaginationException
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

    return fractal($roles, $this->roleTransformer);
  }

  /**
   * Update a role.
   *
   * @param integer $id
   * @return \Domain\Role\Role
   *
   * @throws \Domain\Role\Exceptions\RoleNotFoundException
   * @throws \Domain\Role\Exceptions\CannotUpdateRoleException
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

      return fractal($role, $this->roleTransformer);
    } catch (RoleNotFoundException $e) {
      throw $e->setContext([
        'id' => [
          __('role.id.not_found')
        ]
      ]);
    }
  }
}
