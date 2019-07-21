<?php

namespace Domain\Role\Exceptions;

use Infrastructure\Exceptions\Http\ValidationError;

class RoleAssignedException extends ValidationError
{
  /**
   * Create a new role assigned exception instance.
   */
  public function __construct() {
    parent::__construct(__('role.exceptions.role_assigned'));
  }
}
