<?php

namespace Domain\Restaurant\Contracts\Repositories;

use Domain\Restaurant\Exceptions\CannotCreateRestaurantException;
use Domain\Restaurant\Exceptions\CannotUpdateRestaurantException;
use Domain\Restaurant\Exceptions\RestaurantNotFoundException;
use Domain\Restaurant\Restaurant;
use Illuminate\Database\Eloquent\Collection;
use Support\Contracts\Repositories\AppRepository;

interface RestaurantRepository extends AppRepository
{
    /**
     * Retrieve all of the restaurants.
     *
     * @return Collection
     */
    public function all();

    /**
     * Create a new restaurant.
     *
     * @param array $attributes
     * @return Restaurant
     *
     * @throws CannotCreateRestaurantException
     */
    public function create($attributes);

    /**
     * Create a restaurant if the specified search parameters could not find one
     * with the matching criteria.
     *
     * @param number|string $parameter
     * @param number|string $search
     * @param boolean $regex
     * @param array $attributes
     * @return Restaurant
     *
     * @throws CannotCreateRestaurantException
     */
    public function firstOrCreate($parameter, $search, $regex = true, $attributes = []);

    /**
     * Find a restaurant by an unknown parameter.
     *
     * @param number|string $parameter
     * @param number|string|array $search
     * @param boolean $regex
     * @return Collection
     */
    public function find($parameter, $search, $regex = true);

    /**
     * Find a restaurant by identifier.
     *
     * @param string $id
     * @return Restaurant
     *
     * @throws RestaurantNotFoundException
     */
    public function findById($id);

    /**
     * Update a restaurant.
     *
     * @param Restaurant $restaurant
     * @param array $attributes
     * @return Restaurant
     *
     * @throws CannotUpdateRestaurantException
     */
    public function update(Restaurant $restaurant, $attributes);
}
