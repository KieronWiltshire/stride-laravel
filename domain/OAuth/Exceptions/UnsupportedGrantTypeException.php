<?php

namespace Domain\OAuth\Exceptions;

use Support\Exceptions\Http\BadRequestError;

class UnsupportedGrantTypeException extends BadRequestError
{
    /**
     * Create a new unsupported grant type exception instance.
     */
    public function __construct()
    {
        parent::__construct(__('oauth.exceptions.unsupported_grant_type'));
    }
}
