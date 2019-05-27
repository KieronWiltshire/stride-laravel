<?php

namespace App\Exceptions\Http;

use App\Exceptions\AppException;

class NotFoundError extends AppException
{
  /**
   * Constructor.
   */
  public function __construct()
  {
    parent::__construct(__('http-error.not_found'), 404, 'NotFoundError');
  }
}
