<?php

namespace Domain\OAuth\Exceptions;

use Infrastructure\Exceptions\Http\ValidationError;

class CannotCreateTokenException extends ValidationError
{
  /**
   * Create a new cannot create personal access token exception instance.
   */
  public function __construct() {
    parent::__construct(__('oauth.exceptions.cannot_create_token'));
  }
}
