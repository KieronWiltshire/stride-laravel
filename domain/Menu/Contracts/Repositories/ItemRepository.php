<?php


namespace Domain\Menu\Contracts\Repositories;

use Domain\Menu\Exceptions\CannotCreateItemException;
use Domain\Menu\Exceptions\CannotUpdateItemException;
use Domain\Menu\Exceptions\ItemNotFoundException;
use Domain\Menu\Menu;
use Domain\Menu\Item;
use Illuminate\Database\Eloquent\Collection;
use Support\Contracts\Repositories\AppRepository;

interface ItemRepository extends AppRepository
{
  /**
   * Retrieve all of the items.
   *
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Menu\Item>
   */
  function all();

  /**
   * Create a new item.
   *
   * @param array $attributes
   * @return \Domain\Menu\Item
   *
   * @throws \Domain\Menu\Exceptions\CannotCreateItemException
   */
  function create($attributes);

  /**
   * Create a item if the specified search parameters could not find one
   * with the matching criteria.
   *
   * @param number|string $parameter
   * @param number|string $search
   * @param boolean $regex
   * @param array $attributes
   * @return \Domain\Menu\Item
   *
   * @throws \Domain\Menu\Exceptions\CannotCreateItemException
   */
  function firstOrCreate($parameter, $search, $regex = true, $attributes = []);

  /**
   * Find a item by an unknown parameter.
   *
   * @param number|string $parameter
   * @param number|string|array $search
   * @param boolean $regex
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Menu\Item>
   */
  function find($parameter, $search, $regex = true);

  /**
   * Find a item by identifier.
   *
   * @param string $id
   * @return \Domain\Menu\Item
   *
   * @throws \Domain\Menu\Exceptions\ItemNotFoundException
   */
  function findById($id);

  /**
   * Update a Item.
   *
   * @param \Domain\Menu\Item $item
   * @param array $attributes
   * @return \Domain\Menu\Item
   *
   * @throws \Domain\Menu\Exceptions\CannotUpdateItemException
   */
  function update(Item $item, $attributes);

  /**
   * Retrieve all of the items for the specified menu.
   *
   * @param \Domain\Menu\Menu $menu
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Menu\Item>
   */
  function getItemsForMenu(Menu $menu);
}
