<?php

namespace App\Exceptions\Auth;

use App\Exceptions\Http\UnauthorizedError;

class AuthenticationFailedException extends UnauthorizedError
{
  /**
   * Create a new authentication failed exception instance.
   */
  public function __construct() {
    parent::__construct(__('auth.exceptions.authentication_failed'));
  }
}
