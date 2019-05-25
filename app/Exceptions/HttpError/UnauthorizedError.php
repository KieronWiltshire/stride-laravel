<?php

namespace App\Exceptions\HttpError;

use App\Exceptions\AppException;

class UnauthorizedError extends AppException
{
  /**
   * Constructor.
   */
  public function __construct()
  {
    parent::__construct(__('http-error.unauthorized'), 401, 'UnauthorizedError');
  }
}
