<?php

namespace Domain\Restaurant\Exceptions;

use Support\Exceptions\Http\NotFoundError;

class RestaurantNotFoundException extends NotFoundError
{
    /**
     * Create a new restaurant not found exception instance.
     */
    public function __construct()
    {
        parent::__construct(__('restaurant.exceptions.restaurant_not_found'));
    }
}
