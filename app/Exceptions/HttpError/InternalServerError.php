<?php

namespace App\Exceptions\HttpError;

use App\Exceptions\AppException;

class InternalServerError extends AppException
{
  /**
   * Constructor.
   */
  public function __construct()
  {
    parent::__construct(__('http-error.internal'), 500, 'InternalServerError');
  }
}
