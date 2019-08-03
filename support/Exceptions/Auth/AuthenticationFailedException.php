<?php

namespace Support\Exceptions\Auth;

use Support\Exceptions\Http\UnauthorizedError;

class AuthenticationFailedException extends UnauthorizedError
{
  /**
   * Create a new authentication failed exception instance.
   */
  public function __construct() {
    parent::__construct(__('auth.exceptions.authentication_failed'));
  }
}
