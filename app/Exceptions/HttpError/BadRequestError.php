<?php

namespace App\Exceptions\HttpError;

use App\Exceptions\AppException;

class BadRequestError extends AppException
{
  /**
   * Constructor.
   */
  public function __construct()
  {
    parent::__construct(__('http-error.bad-request'), 400, 'BadRequestError');
  }
}
