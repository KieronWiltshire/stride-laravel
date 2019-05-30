<?php

namespace App\Exceptions\Http;

use App\Exceptions\AppError;

class BadRequestError extends AppError
{
  /**
   * Constructor.
   */
  public function __construct()
  {
    parent::__construct(__('http-error.bad_request'), 400, 'BadRequestError');
  }
}
