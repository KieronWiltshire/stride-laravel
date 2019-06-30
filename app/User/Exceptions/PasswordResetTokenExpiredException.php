<?php

namespace App\User\Exceptions;

use App\Exceptions\Http\ValidationError;

class PasswordResetTokenExpiredException extends ValidationError
{
  /**
   * Create a new password reset token expired exception instance.
   */
  public function __construct() {
    parent::__construct(__('user.exceptions.password_reset_token_expired'));
  }
}
