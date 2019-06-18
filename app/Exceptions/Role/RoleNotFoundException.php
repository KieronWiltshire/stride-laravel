<?php

namespace App\Exceptions\Role;

use App\Exceptions\Http\NotFoundError;

class RoleNotFoundException extends NotFoundError
{
  /**
   * Create a new role not found exception instance.
   */
  public function __construct() {
    parent::__construct(__('role.exceptions.not_found'));
  }
}