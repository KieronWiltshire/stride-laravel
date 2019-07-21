<?php

namespace Domain\User\Exceptions;

use Infrastructure\Exceptions\Http\ValidationError;

class CannotUpdateUserException extends ValidationError
{
  /**
   * Create a new cannot update user exception instance.
   */
  public function __construct() {
    parent::__construct(__('user.exceptions.cannot_update_user'));
  }
}
