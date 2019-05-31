<?php

namespace App\Exceptions\User;

use App\Exceptions\Http\ValidationError;

class PasswordResetTokenExpiredException extends ValidationError
{
  public function __construct() {
    parent::__construct(__('user.exceptions.password_reset_token_expired'));
  }
}
