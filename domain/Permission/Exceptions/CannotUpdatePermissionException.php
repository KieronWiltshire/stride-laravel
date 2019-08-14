<?php

namespace Domain\Permission\Exceptions;

use Support\Exceptions\Http\ValidationError;

class CannotUpdatePermissionException extends ValidationError
{
    /**
     * Create a new cannot update permission exception instance.
     */
    public function __construct()
    {
        parent::__construct(__('permission.exceptions.cannot_update_permission'));
    }
}
