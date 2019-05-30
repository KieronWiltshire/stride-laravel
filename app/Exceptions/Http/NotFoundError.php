<?php

namespace App\Exceptions\Http;

use App\Exceptions\AppError;

class NotFoundError extends AppError
{
  /**
   * Constructor.
   */
  public function __construct()
  {
    parent::__construct(__('http-error.not_found'), 404, 'NotFoundError');
  }
}
