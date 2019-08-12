<?php

namespace Domain\Menu;

use Domain\Menu\Events\ItemCreatedEvent;
use Domain\Menu\Events\ItemUpdatedEvent;
use Domain\Menu\Exceptions\CannotCreateItemException;
use Domain\Menu\Exceptions\CannotUpdateItemException;
use Domain\Menu\Exceptions\ItemNotFoundException;
use Domain\Menu\Item;
use Domain\Menu\Menu;
use Domain\Menu\ItemGroup;
use Domain\Menu\ItemOption;
use Domain\Menu\Validators\ItemCreateValidator;
use Domain\Menu\Validators\ItemUpdateValidator;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Support\Repositories\AppRepository;
use Domain\Menu\Contracts\Repositories\ItemRepository as ItemRepositoryInterface;

class ItemRepository extends AppRepository implements ItemRepositoryInterface
{
  /**
   * @var \Domain\Menu\Validators\ItemCreateValidator
   */
  protected $itemCreateValidator;

  /**
   * @var \Domain\Menu\Validators\ItemUpdateValidator
   */
  protected $itemUpdateValidator;

  /**
   * Create a new item repository instance.
   *
   * @param \Domain\Menu\Validators\ItemCreateValidator $itemCreateValidator
   * @param \Domain\Menu\Validators\ItemUpdateValidator $itemUpdateValidator
   */
  public function __construct(
    ItemCreateValidator $itemCreateValidator,
    ItemUpdateValidator $itemUpdateValidator
  ) {
    $this->itemCreateValidator = $itemCreateValidator;
    $this->itemUpdateValidator = $itemUpdateValidator;
  }

  /**
   * Retrieve all of the items.
   *
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Menu\Item>
   */
  function all()
  {
    return $this->execute(Menu::query());
  }

  /**
   * Create a new item.
   *
   * @param array $attributes
   * @return \Domain\Menu\Item
   *
   * @throws \Domain\Menu\Exceptions\CannotCreateItemException
   */
  function create($attributes)
  {
    $this->itemCreateValidator->validate($attributes);

    if ($item = Item::create($attributes)) {
      event(new ItemCreatedEvent($item));

      return $item;
    }

    throw new Exception();
  }

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
  function firstOrCreate($parameter, $search, $regex = true, $attributes = [])
  {
    $query = Item::query();

    if ($regex) {
      $query->where($parameter, 'REGEXP', $search);
    } else {
      $query->where($parameter, $search);
    }

    $item = $this->execute($query, true);

    return ($item) ? $item : $this->create($attributes);
  }

  /**
   * Find a item by an unknown parameter.
   *
   * @param number|string $parameter
   * @param number|string|array $search
   * @param boolean $regex
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Menu\Item>
   */
  function find($parameter, $search, $regex = true)
  {
    $query = Item::query();

    if (is_array($parameter)) {
      $query->whereIn($parameter, $search);
    } else {
      if ($regex) {
        $query->where($parameter, 'REGEXP', $search);
      } else {
        $query->where($parameter, $search);
      }
    }

    return $this->execute($query);
  }

  /**
   * Find a item by identifier.
   *
   * @param string $id
   * @return \Domain\Menu\Item
   *
   * @throws \Domain\Menu\Exceptions\ItemNotFoundException
   */
  function findById($id)
  {
    $item = $this->execute(Item::where('id', $id), true);

    if (!$item) {
      throw new ItemNotFoundException();
    }

    return $item;
  }

  /**
   * Update a item.
   *
   * @param \Domain\Menu\Item $item
   * @param array $attributes
   * @return \Domain\Menu\Item
   *
   * @throws \Domain\Menu\Exceptions\CannotUpdateItemException
   */
  function update(Item $item, $attributes)
  {
    $this->itemUpdateValidator->validate($attributes);

    foreach ($attributes as $attr => $value) {
      $item->$attr = $value;
    }

    if ($item->save()) {
      event(new ItemUpdatedEvent($item, $attributes));

      return $item;
    }
  }

  /**
   * Retrieve all of the items for the specified menu.
   *
   * @param \Domain\Menu\Menu $menu
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Menu\Item>
   */
  function getItemsForMenu(Menu $menu)
  {
    // TODO: Implement getMenusForRestaurant() method.
    return $this->execute(Menu::where('menu_id', $menu->id), true);
  }
}
