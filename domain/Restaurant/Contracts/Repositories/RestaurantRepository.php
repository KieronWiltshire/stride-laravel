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
     * @return \Illuminate\Database\Eloquent\Collection<\Domain\Restaurant\Restaurant>
     */
    public function all();

    /**
     * Create a new restaurant.
     *
     * @param array $attributes
     * @return \Domain\Restaurant\Restaurant
     *
     * @throws \Domain\Restaurant\Exceptions\CannotCreateRestaurantException
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
     * @return \Domain\Restaurant\Restaurant
     *
     * @throws \Domain\Restaurant\Exceptions\CannotCreateRestaurantException
     */
    public function firstOrCreate($parameter, $search, $regex = true, $attributes = []);

    /**
     * Find a restaurant by an unknown parameter.
     *
     * @param number|string $parameter
     * @param number|string|array $search
     * @param boolean $regex
     * @return \Illuminate\Database\Eloquent\Collection<\Domain\Restaurant\Restaurant>
     */
    public function find($parameter, $search, $regex = true);

    /**
     * Find a restaurant by identifier.
     *
     * @param string $id
     * @return \Domain\Restaurant\Restaurant
     *
     * @throws \Domain\Restaurant\Exceptions\RestaurantNotFoundException
     */
    public function findById($id);

    /**
     * Update a restaurant.
     *
     * @param \Domain\Restaurant\Restaurant $restaurant
     * @param array $attributes
     * @return \Domain\Restaurant\Restaurant
     *
     * @throws \Domain\Restaurant\Exceptions\CannotUpdateRestaurantException
     */
    public function update(Restaurant $restaurant, $attributes);
}
