<?php

namespace App\Exceptions\OAuth;

use App\Exceptions\Http\UnauthorizedError;

class InvalidRefreshTokenException extends UnauthorizedError
{
  public function __construct() {
    parent::__construct(__('oauth.exceptions.invalid_refresh_token'));
  }
}
