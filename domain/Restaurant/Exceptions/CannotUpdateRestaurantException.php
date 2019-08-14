<?php

namespace Domain\Restaurant\Exceptions;

use Support\Exceptions\Http\ValidationError;

class CannotUpdateRestaurantException extends ValidationError
{
    /**
     * Create a new cannot update restaurant exception instance.
     */
    public function __construct()
    {
        parent::__construct(__('restaurant.exceptions.cannot_update_restaurant'));
    }
}
