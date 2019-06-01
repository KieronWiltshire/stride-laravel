<?php

namespace App\Exceptions\Auth;

use App\Exceptions\Http\UnauthorizedError;

class AuthenticationRequiredException extends UnauthorizedError
{
  /**
   * Create a new authentication required exception instance.
   * 
   * @return void
   */
  public function __construct() {
    parent::__construct(__('auth.exceptions.authentication_required'));
  }
}
