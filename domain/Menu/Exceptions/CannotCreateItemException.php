<?php

namespace Domain\Menu\Exceptions;

use Support\Exceptions\Http\ValidationError;

class CannotCreateItemException extends ValidationError
{
    /**
     * Create a new cannot create item exception instance.
     */
    public function __construct()
    {
        parent::__construct(__('menu.exceptions.cannot_create_item'));
    }
}
