<?php

namespace App\Exceptions\OAuth;

use App\Exceptions\Http\NotFoundError;

class TokenNotFoundException extends NotFoundError
{
  /**
   * Create a new token not found exception instance.
   */
  public function __construct() {
    parent::__construct(__('oauth.exceptions.token_not_found'));
  }
}
