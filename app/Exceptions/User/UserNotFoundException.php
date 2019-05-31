<?php

namespace App\Exceptions\User;

use App\Exceptions\Http\NotFoundError;

class UserNotFoundException extends NotFoundError
{
  public function __construct() {
    parent::__construct(__('user.exceptions.not_found'));
  }
}
