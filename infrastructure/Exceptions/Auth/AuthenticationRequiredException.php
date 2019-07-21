<?php

namespace Infrastructure\Exceptions\Auth;

use Infrastructure\Exceptions\Http\UnauthorizedError;

class AuthenticationRequiredException extends UnauthorizedError
{
  /**
   * Create a new authentication required exception instance.
   */
  public function __construct() {
    parent::__construct(__('auth.exceptions.authentication_required'));
  }
}
