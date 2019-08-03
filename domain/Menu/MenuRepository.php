<?php

namespace Domain\Menu;

use Domain\User\User;
use Support\Repositories\AppRepository;
use Domain\Menu\Contracts\Repositories\MenuRepository as MenuRepositoryInterface;

class UserRepository extends AppRepository implements MenuRepositoryInterface
{
  /**
   * Retrieve all of the menus.
   *
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Menu\Menu>
   */
  function all()
  {
    // TODO: Implement all() method.
  }

  /**
   * Create a new menu.
   *
   * @param array $attributes
   * @return \Domain\Menu\Menu
   *
   * @throws \Domain\Menu\Exceptions\CannotCreateMenuException
   */
  function create($attributes)
  {
    // TODO: Implement create() method.
  }

  /**
   * Create a menu if the specified search parameters could not find one
   * with the matching criteria.
   *
   * @param number|string $parameter
   * @param number|string $search
   * @param boolean $regex
   * @param array $attributes
   * @return \Domain\Menu\Menu
   *
   * @throws \Domain\Menu\Exceptions\CannotCreateMenuException
   */
  function firstOrCreate($parameter, $search, $regex = true, $attributes = [])
  {
    // TODO: Implement firstOrCreate() method.
  }

  /**
   * Find a menu by an unknown parameter.
   *
   * @param number|string $parameter
   * @param number|string|array $search
   * @param boolean $regex
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Menu\Menu>
   */
  function find($parameter, $search, $regex = true)
  {
    // TODO: Implement find() method.
  }

  /**
   * Find a user by identifier.
   *
   * @param string $id
   * @return \Domain\Menu\Menu
   *
   * @throws \Domain\Menu\Exceptions\MenuNotFoundException
   */
  function findById($id)
  {
    // TODO: Implement findById() method.
  }

  /**
   * Update a menu.
   *
   * @param \Domain\Menu\Menu $menu
   * @param array $attributes
   * @return \Domain\Menu\Menu
   *
   * @throws \Domain\Menu\Exceptions\CannotUpdateMenuException
   */
  function update(Menu $menu, $attributes)
  {
    // TODO: Implement update() method.
  }

  /**
   * Retrieve all of the menus for the specified user.
   *
   * @param \Domain\User\User $user
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Menu\Menu>
   */
  function getMenusForUser(User $user)
  {
    // TODO: Implement getMenusForUser() method.
  }
}