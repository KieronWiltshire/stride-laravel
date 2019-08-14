<?php

namespace Support\Exceptions\Http;

use Support\Exceptions\AppError;

class InternalServerError extends AppError
{
    /**
     * Create a new internal server error instance.
     *
     * @param string $message
     * @throws \Exception
     */
    public function __construct($message = null)
    {
        parent::__construct($message ?: __('http.exceptions.internal'), 500, 'InternalServerError');
    }
}
