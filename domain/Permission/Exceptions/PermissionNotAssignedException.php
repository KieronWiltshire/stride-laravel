<?php

namespace Domain\Permission\Exceptions;

use Infrastructure\Exceptions\Http\ValidationError;

class PermissionNotAssignedException extends ValidationError
{
  /**
   * Create a new permission not assigned exception instance.
   */
  public function __construct() {
    parent::__construct(__('permission.exceptions.permission_not_assigned'));
  }
}
