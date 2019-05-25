<?php

namespace App\Exceptions\HttpError;

use App\Exceptions\AppException;

class ForbiddenError extends AppException
{
  /**
   * Constructor.
   */
  public function __construct()
  {
    parent::__construct(__('http-error.forbidden'), 403, 'ForbiddenError');
  }
}
