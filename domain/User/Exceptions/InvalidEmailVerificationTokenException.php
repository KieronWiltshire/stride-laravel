<?php

namespace Domain\User\Exceptions;

use Infrastructure\Exceptions\Http\ValidationError;

class InvalidEmailVerificationTokenException extends ValidationError
{
  /**
   * Create a new invalid email verification token exception instance.
   */
  public function __construct() {
    parent::__construct(__('user.exceptions.invalid_email_verification_token'));
  }
}
