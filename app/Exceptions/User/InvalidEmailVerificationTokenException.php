<?php

namespace App\Exceptions\User;

use App\Exceptions\Http\ValidationError;

class InvalidEmailVerificationTokenException extends ValidationError
{
  /**
   * Create a new invalid email verification token exception instance.
   * 
   * @return void
   */
  public function __construct() {
    parent::__construct(__('user.exceptions.invalid_email_verification_token'));
  }
}
