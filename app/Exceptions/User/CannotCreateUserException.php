<?php

namespace App\Exceptions\User;

use App\Exceptions\Http\ValidationError;

class CannotCreateUserException extends ValidationError
{
  /**
   * Create a new cannot create user exception instance.
   * 
   * @return void
   */
  public function __construct() {
    parent::__construct(__('user.exceptions.cannot_create_user'));
  }
}
