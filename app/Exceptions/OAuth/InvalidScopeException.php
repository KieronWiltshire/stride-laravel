<?php

namespace App\Exceptions\OAuth;

use App\Exceptions\Http\BadRequestError;

class InvalidScopeException extends BadRequestError
{
  /**
   * Create a new invalid scope exception instance.
   * 
   * @return void
   */
  public function __construct() {
    parent::__construct(__('oauth.exceptions.invalid_scope'));
  }
}
