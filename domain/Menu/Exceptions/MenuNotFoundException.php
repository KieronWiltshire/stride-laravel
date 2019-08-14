<?php

namespace Domain\Menu\Exceptions;

use Support\Exceptions\Http\NotFoundError;

class MenuNotFoundException extends NotFoundError
{
    /**
     * Create a new menu not found exception instance.
     */
    public function __construct()
    {
        parent::__construct(__('menu.exceptions.menu_not_found'));
    }
}
