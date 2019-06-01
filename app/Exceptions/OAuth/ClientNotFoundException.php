<?php

namespace App\Exceptions\OAuth;

use App\Exceptions\Http\NotFoundError;

class ClientNotFoundException extends NotFoundError
{
  /**
   * Create a new client not found exception instance.
   * 
   * @return void
   */
  public function __construct() {
    parent::__construct(__('oauth.exceptions.client_not_found'));
  }
}
