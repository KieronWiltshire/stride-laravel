<?php

namespace Domain\Role\Exceptions;

use Infrastructure\Exceptions\Http\NotFoundError;

class UnableToSetDefaultRoleException extends NotFoundError
{
  /**
   * Create a new unable to set default role exception instance.
   */
  public function __construct() {
    parent::__construct(__('role.exceptions.unable_to_set_default_role'));
  }
}