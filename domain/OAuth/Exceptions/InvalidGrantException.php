<?php

namespace Domain\OAuth\Exceptions;

use Support\Exceptions\Http\BadRequestError;

class InvalidGrantException extends BadRequestError
{
  /**
   * Create a new invalid grant exception instance.
   */
  public function __construct() {
    parent::__construct(__('oauth.exceptions.invalid_grant'));
  }
}
