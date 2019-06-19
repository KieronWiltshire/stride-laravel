<?php

namespace App\Exceptions\Permission;

use App\Exceptions\Http\ValidationError;

class CannotCreatePermissionException extends ValidationError
{
  /**
   * Create a new cannot create permission exception instance.
   */
  public function __construct() {
    parent::__construct(__('permission.exceptions.cannot_create_permission'));
  }
}