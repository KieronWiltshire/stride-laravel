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
     * @return Collection
     */
    public function all();

    /**
     * Create a new item.
     *
     * @param array $attributes
     * @return Item
     *
     * @throws CannotCreateItemException
     */
    public function create($attributes);

    /**
     * Create a item if the specified search parameters could not find one
     * with the matching criteria.
     *
     * @param number|string $parameter
     * @param number|string $search
     * @param boolean $regex
     * @param array $attributes
     * @return Item
     *
     * @throws CannotCreateItemException
     */
    public function firstOrCreate($parameter, $search, $regex = true, $attributes = []);

    /**
     * Find a item by an unknown parameter.
     *
     * @param number|string $parameter
     * @param number|string|array $search
     * @param boolean $regex
     * @return Collection
     */
    public function find($parameter, $search, $regex = true);

    /**
     * Find a item by identifier.
     *
     * @param string $id
     * @return Item
     *
     * @throws ItemNotFoundException
     */
    public function findById($id);

    /**
     * Update a Item.
     *
     * @param Item $item
     * @param array $attributes
     * @return Item
     *
     * @throws CannotUpdateItemException
     */
    public function update(Item $item, $attributes);

    /**
     * Retrieve all of the items for the specified menu.
     *
     * @param Menu $menu
     * @return Collection
     */
    public function getItemsForMenu(Menu $menu);
}
