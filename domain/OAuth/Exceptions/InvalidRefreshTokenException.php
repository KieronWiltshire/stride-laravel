<?php

namespace Domain\OAuth\Exceptions;

use Support\Exceptions\Http\UnauthorizedError;

class InvalidRefreshTokenException extends UnauthorizedError
{
  /**
   * Create a new invalid refresh token exception instance.
   */
  public function __construct() {
    parent::__construct(__('oauth.exceptions.invalid_refresh_token'));
  }
}
