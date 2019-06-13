<?php

namespace App\Exceptions\User;

use App\Exceptions\Http\NotFoundError;

class UserNotFoundException extends NotFoundError
{
  /**
   * Create a new user not found exception instance.
   */
  public function __construct() {
    parent::__construct(__('user.exceptions.not_found'));
  }
}
