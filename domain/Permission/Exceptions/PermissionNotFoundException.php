<?php

namespace Domain\Permission\Exceptions;

use Support\Exceptions\Http\NotFoundError;

class PermissionNotFoundException extends NotFoundError
{
    /**
     * Create a new permission not found exception instance.
     */
    public function __construct()
    {
        parent::__construct(__('permission.exceptions.not_found'));
    }
}
