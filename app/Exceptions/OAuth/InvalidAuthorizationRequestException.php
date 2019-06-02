<?php

namespace App\Exceptions\OAuth;

use App\Exceptions\Http\BadRequestError;

class InvalidAuthorizationRequestException extends BadRequestError
{
  /**
   * Create a new invalid authorization request exception instance.
   *
   * @return void
   */
  public function __construct() {
    parent::__construct(__('oauth.exceptions.invalid_authorization_request'));
  }
}
