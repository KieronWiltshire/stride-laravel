<?php

namespace Support\Exceptions\Http;

use Support\Exceptions\AppError;

class TooManyRequestsError extends AppError
{
    /**
     * Create a new validation error instance.
     *
     * @param string $message
     * @throws \Exception
     */
    public function __construct($message = null)
    {
        parent::__construct($message ?: __('http.exceptions.too_many_requests'), 429, 'TooManyRequestsError');
    }
}
