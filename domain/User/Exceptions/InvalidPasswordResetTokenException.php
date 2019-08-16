<?php

namespace Domain\User\Exceptions;

use Support\Exceptions\Http\ValidationError;

class InvalidPasswordResetTokenException extends ValidationError
{
    /**
     * Create a new invalid password reset token exception instance.
     */
    public function __construct()
    {
        parent::__construct(__('user.exceptions.invalid_password_reset_token'));
    }
}
