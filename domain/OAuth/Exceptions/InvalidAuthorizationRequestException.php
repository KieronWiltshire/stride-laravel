<?php

namespace Domain\OAuth\Exceptions;

use Support\Exceptions\Http\BadRequestError;

class InvalidAuthorizationRequestException extends BadRequestError
{
    /**
     * Create a new invalid authorization request exception instance.
     */
    public function __construct()
    {
        parent::__construct(__('oauth.exceptions.invalid_authorization_request'));
    }
}
