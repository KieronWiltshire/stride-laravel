<?php

namespace App\Exceptions\User;

use App\Exceptions\Http\ValidationError;

class InvalidPasswordException extends ValidationError
{
  /**
   * Create a new invalid password exception instance.
   * 
   * @return void
   */
  public function __construct() {
    parent::__construct(__('user.exceptions.invalid_password'));
  }
}
