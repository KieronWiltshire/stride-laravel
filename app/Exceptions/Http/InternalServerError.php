<?php

namespace App\Exceptions\Http;

use App\Exceptions\AppError;

class InternalServerError extends AppError
{
  /**
   * Constructor.
   */
  public function __construct()
  {
    parent::__construct(__('http-error.internal'), 500, 'InternalServerError');
  }
}
