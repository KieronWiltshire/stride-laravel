<?php

namespace Domain\Restaurant\Exceptions;

use Support\Exceptions\Http\ValidationError;

class CannotCreateRestaurantException extends ValidationError
{
    /**
     * Create a new cannot create restaurant exception instance.
     */
    public function __construct()
    {
        parent::__construct(__('restaurant.exceptions.cannot_create_restaurant'));
    }
}
