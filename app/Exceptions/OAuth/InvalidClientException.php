<?php

namespace App\Exceptions\OAuth;

use App\Exceptions\Http\UnauthorizedError;

class InvalidClientException extends UnauthorizedError
{
  /**
   * Create a new invalid client exception instance.
   */
  public function __construct() {
    parent::__construct(__('oauth.exceptions.invalid_client'));
  }
}
