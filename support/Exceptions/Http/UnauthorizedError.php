<?php

namespace Support\Exceptions\Http;

use Support\Exceptions\AppError;

class UnauthorizedError extends AppError
{
    /**
     * Create a new unauthorized error instance.
     *
     * @param string $message
     * @throws \Exception
     */
    public function __construct($message = null)
    {
        parent::__construct($message ?: __('http.exceptions.unauthorized'), 401, 'UnauthorizedError');
    }
}
