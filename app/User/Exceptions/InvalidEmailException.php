<?php

namespace App\User\Exceptions;

use App\Exceptions\Http\ValidationError;

class InvalidEmailException extends ValidationError
{
  /**
   * Create a new invalid email exception instance.
   */
  public function __construct() {
    parent::__construct(__('user.exceptions.invalid_email'));
  }
}
