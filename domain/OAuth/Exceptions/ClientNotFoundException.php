<?php

namespace Domain\OAuth\Exceptions;

use Support\Exceptions\Http\NotFoundError;

class ClientNotFoundException extends NotFoundError
{
  /**
   * Create a new client not found exception instance.
   */
  public function __construct() {
    parent::__construct(__('oauth.exceptions.client_not_found'));
  }
}
