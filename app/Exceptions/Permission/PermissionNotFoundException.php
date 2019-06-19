<?php

namespace App\Exceptions\Permission;

use App\Exceptions\Http\NotFoundError;

class PermissionNotFoundException extends NotFoundError
{
  /**
   * Create a new permission not found exception instance.
   */
  public function __construct() {
    parent::__construct(__('permission.exceptions.not_found'));
  }
}