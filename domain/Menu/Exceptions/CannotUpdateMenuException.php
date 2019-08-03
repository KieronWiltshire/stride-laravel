<?php

namespace Domain\Menu\Exceptions;

use Support\Exceptions\Http\ValidationError;

class CannotUpdateMenuException extends ValidationError
{
  /**
   * Create a new cannot update menu exception instance.
   */
  public function __construct() {
    parent::__construct(__('menu.exceptions.cannot_update_menu'));
  }
}
