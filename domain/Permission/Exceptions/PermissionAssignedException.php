<?php

namespace Domain\Permission\Exceptions;

use Infrastructure\Exceptions\Http\ValidationError;

class PermissionAssignedException extends ValidationError
{
  /**
   * Create a new permission assigned exception instance.
   */
  public function __construct() {
    parent::__construct(__('permission.exceptions.permission_assigned'));
  }
}
