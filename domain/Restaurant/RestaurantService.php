<?php

namespace Domain\Restaurant;

use Domain\Restaurant\Contracts\Repositories\RestaurantRepository;
use Domain\Restaurant\Exceptions\CannotCreateRestaurantException;
use Domain\Restaurant\Exceptions\CannotUpdateRestaurantException;
use Domain\Restaurant\Exceptions\RestaurantNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Support\Exceptions\Pagination\InvalidPaginationException;

class RestaurantService
{
    /**
     * @var RestaurantRepository
     */
    protected $restaurantRepository;

    /**
     * Create a new restaurant service instance.
     *
     * @param RestaurantRepository $restaurantRepository
     */
    public function __construct(
        RestaurantRepository $restaurantRepository
    ) {
        $this->restaurantRepository = $restaurantRepository;
    }

    /**
     * Retrieve all of the restaurants.
     *
     * @return Collection
     */
    public function all()
    {
        return $this->restaurantRepository->all();
    }

    /**
     * Create a new restaurant.
     *
     * @param array $attributes
     * @return Restaurant
     *
     * @throws CannotCreateRestaurantException
     */
    public function create($attributes)
    {
        return $this->restaurantRepository->create($attributes);
    }

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
    public function firstOrCreate($parameter, $search, $regex = true, $attributes = [])
    {
        return $this->restaurantRepository->firstOrCreate($parameter, $search, $regex, $attributes);
    }

    /**
     * Find a restaurant by an unknown parameter.
     *
     * @param number|string $parameter
     * @param number|string|array $search
     * @param boolean $regex
     * @return Collection
     */
    public function find($parameter, $search, $regex = true)
    {
        return $this->restaurantRepository->find($parameter, $search, $regex);
    }

    /**
     * Find a restaurant by identifier.
     *
     * @param string $id
     * @return Restaurant
     *
     * @throws RestaurantNotFoundException
     */
    public function findById($id)
    {
        return $this->restaurantRepository->findById($id);
    }

    /**
     * Update a restaurant.
     *
     * @param Restaurant $restaurant
     * @param array $attributes
     * @return Restaurant
     *
     * @throws CannotUpdateRestaurantException
     */
    public function update(Restaurant $restaurant, $attributes)
    {
        return $this->restaurantRepository->update($restaurant, $attributes);
    }

    /**
     * Retrieve an index of the restaurant.
     *
     * @param integer $limit
     * @param integer $offset
     * @return LengthAwarePaginator
     * @throws InvalidPaginationException
     */
    public function index($limit = null, $offset = 1)
    {
        return $this->restaurantRepository->with(['permissions'])->paginate($limit, $offset)->all();
    }
}
