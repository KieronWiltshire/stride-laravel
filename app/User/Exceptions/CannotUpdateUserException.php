<?php

namespace App\User\Exceptions;

use App\Exceptions\Http\ValidationError;

class CannotUpdateUserException extends ValidationError
{
  /**
   * Create a new cannot update user exception instance.
   */
  public function __construct() {
    parent::__construct(__('user.exceptions.cannot_update_user'));
  }
}
