<?php

namespace Support\Exceptions\Http;

use Support\Exceptions\AppError;

class ValidationError extends AppError
{
    /**
     * Create a new validation error instance.
     *
     * @param string $message
     * @throws \Exception
     */
    public function __construct($message = null)
    {
        parent::__construct($message ?: __('http.exceptions.validation'), 422, 'ValidationError');
    }
}
