<?php

namespace Domain\Menu\Exceptions;

use Support\Exceptions\Http\ValidationError;

class CannotUpdateItemException extends ValidationError
{
    /**
     * Create a new cannot update item exception instance.
     */
    public function __construct()
    {
        parent::__construct(__('menu.exceptions.cannot_update_item'));
    }
}
