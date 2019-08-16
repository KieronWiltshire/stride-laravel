<?php

namespace Domain\User\Exceptions;

use Support\Exceptions\Http\ValidationError;

class InvalidPasswordException extends ValidationError
{
    /**
     * Create a new invalid password exception instance.
     */
    public function __construct()
    {
        parent::__construct(__('user.exceptions.invalid_password'));
    }
}
