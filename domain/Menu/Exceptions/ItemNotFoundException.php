<?php

namespace Domain\Menu\Exceptions;

use Support\Exceptions\Http\NotFoundError;

class ItemNotFoundException extends NotFoundError
{
    /**
     * Create a new item not found exception instance.
     */
    public function __construct()
    {
        parent::__construct(__('menu.exceptions.item_not_found'));
    }
}
