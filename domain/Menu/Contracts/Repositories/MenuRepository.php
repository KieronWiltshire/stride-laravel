<?php

namespace Domain\Menu\Contracts\Repositories;

use Domain\Menu\Exceptions\CannotCreateMenuException;
use Domain\Menu\Exceptions\CannotUpdateMenuException;
use Domain\Menu\Exceptions\RestaurantNotFoundException;
use Domain\Menu\Menu;
use Domain\Restaurant\Restaurant;
use Illuminate\Database\Eloquent\Collection;
use Support\Contracts\Repositories\AppRepository;

interface MenuRepository extends AppRepository
{
    /**
     * Retrieve all of the menus.
     *
     * @return \Illuminate\Database\Eloquent\Collection<\Domain\Menu\Menu>
     */
    public function all();

    /**
     * Create a new menu.
     *
     * @param array $attributes
     * @return \Domain\Menu\Menu
     *
     * @throws \Domain\Menu\Exceptions\CannotCreateMenuException
     */
    public function create($attributes);

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
    public function firstOrCreate($parameter, $search, $regex = true, $attributes = []);

    /**
     * Find a menu by an unknown parameter.
     *
     * @param number|string $parameter
     * @param number|string|array $search
     * @param boolean $regex
     * @return \Illuminate\Database\Eloquent\Collection<\Domain\Menu\Menu>
     */
    public function find($parameter, $search, $regex = true);

    /**
     * Find a menu by identifier.
     *
     * @param string $id
     * @return \Domain\Menu\Menu
     *
     * @throws \Domain\Menu\Exceptions\RestaurantNotFoundException
     */
    public function findById($id);

    /**
     * Update a menu.
     *
     * @param \Domain\Menu\Menu $menu
     * @param array $attributes
     * @return \Domain\Menu\Menu
     *
     * @throws \Domain\Menu\Exceptions\CannotUpdateMenuException
     */
    public function update(Menu $menu, $attributes);

    /**
     * Retrieve all of the menus for the specified restauraunt.
     *
     * @param \Domain\Restaurant\Restaurant $restaurant
     * @return \Illuminate\Database\Eloquent\Collection<\Domain\Menu\Menu>
     */
    public function getMenusForRestaurant(Restaurant $restaurant);
}
