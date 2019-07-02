<?php

namespace Domain\OAuth\Exceptions;

use Infrastructure\Exceptions\Http\BadRequestError;

class InvalidScopeException extends BadRequestError
{
  /**
   * Create a new invalid scope exception instance.
   */
  public function __construct() {
    parent::__construct(__('oauth.exceptions.invalid_scope'));
  }
}
