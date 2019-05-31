<?php

namespace App\Exceptions\User;

use App\Exceptions\Http\ValidationError;

class InvalidPasswordResetTokenException extends ValidationError
{
  public function __construct() {
    parent::__construct(__('user.exceptions.invalid_password_reset_token'));
  }
}
