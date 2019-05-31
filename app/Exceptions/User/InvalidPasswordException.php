<?php

namespace App\Exceptions\User;

use App\Exceptions\Http\ValidationError;

class InvalidPasswordException extends ValidationError
{
  public function __construct() {
    parent::__construct(__('user.exceptions.invalid_password'));
  }
}
