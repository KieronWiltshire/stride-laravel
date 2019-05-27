<?php

namespace App\Exceptions\Http;

use App\Exceptions\AppException;

class BadRequestError extends AppException
{
  /**
   * Constructor.
   */
  public function __construct()
  {
    parent::__construct(__('http-error.bad_request'), 400, 'BadRequestError');
  }
}
