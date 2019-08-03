<?php

namespace Domain\Role\Exceptions;

use Support\Exceptions\Http\ValidationError;

class RoleNotAssignedException extends ValidationError
{
  /**
   * Create a new role not assigned exception instance.
   */
  public function __construct() {
    parent::__construct(__('role.exceptions.role_not_assigned'));
  }
}
