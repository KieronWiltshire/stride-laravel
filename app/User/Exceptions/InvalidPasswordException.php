<?php

namespace App\User\Exceptions;

use App\Exceptions\Http\ValidationError;

class InvalidPasswordException extends ValidationError
{
  /**
   * Create a new invalid password exception instance.
   */
  public function __construct() {
    parent::__construct(__('user.exceptions.invalid_password'));
  }
}
