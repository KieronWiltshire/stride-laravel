<?php

namespace App\Exceptions\Http;

use App\Exceptions\AppError;

class UnauthorizedError extends AppError
{
  /**
   * Constructor.
   */
  public function __construct()
  {
    parent::__construct(__('http-error.unauthorized'), 401, 'UnauthorizedError');
  }
}
