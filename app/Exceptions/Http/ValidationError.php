<?php

namespace App\Exceptions\Http;

use App\Exceptions\AppException;

class ValidationError extends AppException
{
  /**
   * Constructor.
   */
  public function __construct()
  {
    parent::__construct(__('http-error.validation'), 422, 'ValidationError');
  }
}