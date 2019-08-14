<?php

namespace Domain\Restaurant;

use Domain\Restaurant\Events\RestaurantCreatedEvent;
use Domain\Restaurant\Events\RestaurantUpdatedEvent;
use Domain\Restaurant\Exceptions\CannotCreateRestaurantException;
use Domain\Restaurant\Exceptions\CannotUpdateRestaurantException;
use Domain\Restaurant\Exceptions\RestaurantNotFoundException;
use Domain\Restaurant\Validators\RestaurantCreateValidator;
use Domain\Restaurant\Validators\RestaurantUpdateValidator;
use Domain\Restaurant\Restaurant;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Support\Repositories\AppRepository;
use Domain\Restaurant\Contracts\Repositories\RestaurantRepository as RestaurantRepositoryInterface;

class RestaurantRepository extends AppRepository implements RestaurantRepositoryInterface
{
    /**
     * @var \Domain\Restaurant\Validators\RestaurantCreateValidator
     */
    protected $restaurantCreateValidator;

    /**
     * @var \Domain\Restaurant\Validators\RestaurantUpdateValidator
     */
    protected $restaurantUpdateValidator;

    /**
     * Create a new restaurant repository instance.
     *
     * @param \Domain\Restaurant\Validators\RestaurantCreateValidator $restaurantCreateValidator
     * @param \Domain\Restaurant\Validators\RestaurantUpdateValidator $restaurantUpdateValidator
     */
    public function __construct(
      RestaurantCreateValidator $restaurantCreateValidator,
      RestaurantUpdateValidator $restaurantUpdateValidator
  ) {
        $this->restaurantCreateValidator = $restaurantCreateValidator;
        $this->restaurantUpdateValidator = $restaurantUpdateValidator;
    }

    /**
     * Retrieve all of the restaurants.
     *
     * @return \Illuminate\Database\Eloquent\Collection<\Domain\Restaurant\Restaurant>
     */
    public function all()
    {
        return $this->execute(Restaurant::query());
    }

    /**
     * Create a new restaurant.
     *
     * @param array $attributes
     * @return \Domain\Restaurant\Restaurant
     *
     * @throws \Domain\Restaurant\Exceptions\CannotCreateRestaurantException
     */
    public function create($attributes)
    {
        $this->restaurantCreateValidator->validate($attributes);

        if ($menu = Restaurant::create($attributes)) {
            event(new RestaurantCreatedEvent($menu));

            return $menu;
        }

        throw new Exception();
    }

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
    public function firstOrCreate($parameter, $search, $regex = true, $attributes = [])
    {
        $query = Restaurant::query();

        if ($regex) {
            $query->where($parameter, 'REGEXP', $search);
        } else {
            $query->where($parameter, $search);
        }

        $restaurant = $this->execute($query, true);

        return ($restaurant) ? $restaurant : $this->create($attributes);
    }

    /**
     * Find a restaurant by an unknown parameter.
     *
     * @param number|string $parameter
     * @param number|string|array $search
     * @param boolean $regex
     * @return \Illuminate\Database\Eloquent\Collection<\Domain\Restaurant\Restaurant>
     */
    public function find($parameter, $search, $regex = true)
    {
        $query = Restaurant::query();

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
     * Find a restaurant by identifier.
     *
     * @param string $id
     * @return \Domain\Restaurant\Restaurant
     *
     * @throws \Domain\Restaurant\Exceptions\RestaurantNotFoundException
     */
    public function findById($id)
    {
        $restaurant = $this->execute(Restaurant::where('id', $id), true);

        if (!$restaurant) {
            throw new RestaurantNotFoundException();
        }

        return $restaurant;
    }

    /**
     * Update a restaurant.
     *
     * @param \Domain\Restaurant\Restaurant $restaurant
     * @param array $attributes
     * @return \Domain\Restaurant\Restaurant
     *
     * @throws \Domain\Restaurant\Exceptions\CannotUpdateRestaurantException
     */
    public function update(Restaurant $restaurant, $attributes)
    {
        $this->restaurantUpdateValidator->validate($attributes);

        foreach ($attributes as $attr => $value) {
            $restaurant->$attr = $value;
        }

        if ($restaurant->save()) {
            event(new RestaurantUpdatedEvent($restaurant, $attributes));

            return $restaurant;
        }
    }
}
