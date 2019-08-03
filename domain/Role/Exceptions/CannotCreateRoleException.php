<?php

namespace Domain\Role\Exceptions;

use Support\Exceptions\Http\ValidationError;

class CannotCreateRoleException extends ValidationError
{
  /**
   * Create a new cannot create role exception instance.
   */
  public function __construct() {
    parent::__construct(__('role.exceptions.cannot_create_role'));
  }
}