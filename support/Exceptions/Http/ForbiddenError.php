<?php

namespace Support\Exceptions\Http;

use Support\Exceptions\AppError;

class ForbiddenError extends AppError
{
    /**
     * Create a new forbidden error instance.
     *
     * @param string $message
     * @throws \Exception
     */
    public function __construct($message = null)
    {
        parent::__construct($message ?: __('http.exceptions.forbidden'), 403, 'ForbiddenError');
    }
}
