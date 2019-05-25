<?php

namespace App\Exceptions\HttpError;

use App\Exceptions\AppException;

class NotFoundError extends AppException
{
  /**
   * Constructor.
   */
  public function __construct()
  {
    parent::__construct(__('http-error.not-found'), 404, 'NotFoundError');
  }
}
