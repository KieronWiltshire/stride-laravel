<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Role\RoleNotFoundException;
use App\Http\Controllers\Controller;
use App\Contracts\Repositories\RoleRepository;
use App\Exceptions\Http\BadRequestError;
use App\Transformers\RoleTransformer;

class RoleController extends Controller
{
  /**
   * @var \App\Contracts\Repositories\RoleRepository
   */
  protected $roleRepository;

  /**
   * @var \App\Transformers\RoleTransformer
   */
  protected $roleTransformer;

  /**
   * Create a new role controller instance
   *
   * @param \App\Contracts\Repositories\RoleRepository $roleRepository
   * @param \App\Transformers\RoleTransformer $roleTransformer
   */
  public function __construct(
    RoleRepository $roleRepository,
    RoleTransformer $roleTransformer
  ) {
    $this->roleRepository = $roleRepository;
    $this->roleTransformer = $roleTransformer;
  }

  /**
   * Retrieve an index of roles.
   *
   * @return \Illuminate\Pagination\LengthAwarePaginator<\App\Entities\Role>
   *
   * @throws \App\Exceptions\Pagination\InvalidPaginationException
   */
  public function index()
  {
    $roles = $this->roleRepository->allAsPaginated(request()->query('limit'), request()->query('offset'))
      ->setPath(route('api.role.index'))
      ->setPageName('offset')
      ->appends([
        'limit' => request()->query('limit')
      ]);

    return fractal($roles, $this->roleTransformer);
  }

  /**
   * Create a new role.
   *
   * @return \App\Entities\Role
   *
   * @throws \App\Exceptions\Role\CannotCreateRoleException
   */
  public function create()
  {
    $this->authorize('role.create');

    $role = $this->roleRepository->create([
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
   * @return \App\Entities\Role
   *
   * @throws \App\Exceptions\Role\RoleNotFoundException
   */
  public function getById($id)
  {
    try {
      return fractal($this->roleRepository->findById($id), $this->roleTransformer);
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
   * @return \App\Entities\Role
   *
   * @throws \App\Exceptions\Role\RoleNotFoundException
   */
  public function getByName($name)
  {
    try {
      return fractal($this->roleRepository->findByName($name), $this->roleTransformer);
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
   * @return \Illuminate\Pagination\LengthAwarePaginator<\App\Entities\Role>
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

    $roles = $this->roleRepository->findAsPaginated(request()->query('parameter'), request()->query('search'), (bool) request()->query('regex'), request()->query('limit'), request()->query('offset'))
      ->setPath(route('api.role.search'))
      ->setPageName('offset')
      ->appends([
        'limit' => request()->query('limit')
      ]);

    return fractal($roles, $this->roleTransformer);
  }

  /**
   * Update a role.
   *
   * @param integer $id
   * @return \App\Entities\Role
   *
   * @throws \App\Exceptions\Role\RoleNotFoundException
   * @throws \App\Exceptions\Role\CannotUpdateRoleException
   */
  public function update($id)
  {
    try {
      $role = $this->roleRepository->findById($id);
      $this->authorize('role.update', $role);

      $role = $this->roleRepository->update($role, [
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
