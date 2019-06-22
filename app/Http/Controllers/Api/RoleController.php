<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Role\RoleNotFoundException;
use App\Http\Controllers\Controller;
use App\Contracts\Repositories\RoleRepository;
use App\Exceptions\Http\BadRequestError;

class RoleController extends Controller
{
  /**
   * @var \App\Contracts\Repositories\RoleRepository
   */
  private $roleRepository;

  /**
   * Create a new role controller instance
   *
   * @param \App\Contracts\Repositories\RoleRepository $roleRepository
   */
  public function __construct(
    RoleRepository $roleRepository
  ) {
    $this->roleRepository = $roleRepository;
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
    $paginated = $this->roleRepository->allAsPaginated(request()->query('limit'), request()->query('offset'))
      ->setPath(route('api.role.index'))
      ->setPageName('offset')
      ->appends([
        'limit' => request()->query('limit')
      ]);

    return $paginated;
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

    return $this->roleRepository->create([
      'name' => request()->input('name'),
      'display_name' => request()->input('display_name'),
      'description' => request()->input('description')
    ]);
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
      $role = $this->roleRepository->findById($id);

      return $role;
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
      return $this->roleRepository->findByName($name);
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

    $paginated = $this->roleRepository->findAsPaginated(request()->query('parameter'), request()->query('search'), (bool) request()->query('regex'), request()->query('limit'), request()->query('offset'))
      ->setPath(route('api.role.search'))
      ->setPageName('offset')
      ->appends([
        'limit' => request()->query('limit')
      ]);

    return $paginated;
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

      return $this->roleRepository->update($role, [
        'name' => request()->input('name'),
        'display_name' => request()->input('display_name'),
        'description' => request()->input('description')
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
