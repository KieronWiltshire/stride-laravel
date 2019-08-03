<?php

namespace Domain\User\Exceptions;

use Support\Exceptions\Http\ValidationError;

class CannotCreateUserException extends ValidationError
{
  /**
   * Create a new cannot create user exception instance.
   */
  public function __construct() {
    parent::__construct(__('user.exceptions.cannot_create_user'));
  }
}
