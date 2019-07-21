<?php

namespace Domain\Menu\Exceptions;

use Infrastructure\Exceptions\Http\ValidationError;

class CannotCreateMenuException extends ValidationError
{
  /**
   * Create a new cannot create menu exception instance.
   */
  public function __construct() {
    parent::__construct(__('menu.exceptions.cannot_create_menu'));
  }
}
