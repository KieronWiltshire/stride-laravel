<?php

namespace Domain\OAuth\Exceptions;

use Support\Exceptions\Http\ValidationError;

class CannotCreateClientException extends ValidationError
{
  /**
   * Create a new cannot create client exception instance.
   */
  public function __construct() {
    parent::__construct(__('oauth.exceptions.cannot_create_client'));
  }
}
