<?php

namespace Domain\Role\Exceptions;

use Support\Exceptions\Http\ValidationError;

class CannotUpdateRoleException extends ValidationError
{
    /**
     * Create a new cannot update role exception instance.
     */
    public function __construct()
    {
        parent::__construct(__('role.exceptions.cannot_update_permission'));
    }
}
