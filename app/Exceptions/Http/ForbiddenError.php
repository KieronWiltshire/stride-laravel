<?php

namespace App\Exceptions\Http;

use App\Exceptions\AppError;

class ForbiddenError extends AppError
{
  /**
   * Constructor.
   */
  public function __construct()
  {
    parent::__construct(__('http-error.forbidden'), 403, 'ForbiddenError');
  }
}
