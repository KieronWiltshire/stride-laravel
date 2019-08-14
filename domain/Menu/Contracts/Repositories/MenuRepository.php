<?php

namespace Domain\Menu\Contracts\Repositories;

use Domain\Menu\Exceptions\CannotCreateMenuException;
use Domain\Menu\Exceptions\CannotUpdateMenuException;
use Domain\Menu\Exceptions\MenuNotFoundException;
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
     * @return Collection
     */
    public function all();

    /**
     * Create a new menu.
     *
     * @param array $attributes
     * @return Menu
     *
     * @throws CannotCreateMenuException
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
     * @return Menu
     *
     * @throws CannotCreateMenuException
     */
    public function firstOrCreate($parameter, $search, $regex = true, $attributes = []);

    /**
     * Find a menu by an unknown parameter.
     *
     * @param number|string $parameter
     * @param number|string|array $search
     * @param boolean $regex
     * @return Collection
     */
    public function find($parameter, $search, $regex = true);

    /**
     * Find a menu by identifier.
     *
     * @param string $id
     * @return Menu
     *
     * @throws MenuNotFoundException
     */
    public function findById($id);

    /**
     * Update a menu.
     *
     * @param Menu $menu
     * @param array $attributes
     * @return Menu
     *
     * @throws CannotUpdateMenuException
     */
    public function update(Menu $menu, $attributes);

    /**
     * Retrieve all of the menus for the specified restaurant.
     *
     * @param Restaurant $restaurant
     * @return Collection
     */
    public function getMenusForRestaurant(Restaurant $restaurant);
}
