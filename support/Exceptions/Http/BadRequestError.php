<?php

namespace Support\Exceptions\Http;

use Support\Exceptions\AppError;

class BadRequestError extends AppError
{
    /**
     * Create a new bad request error instance.
     *
     * @param string $message
     * @throws \Exception
     */
    public function __construct($message = null)
    {
        parent::__construct($message ?: __('http.exceptions.bad_request'), 400, 'BadRequestError');
    }
}
