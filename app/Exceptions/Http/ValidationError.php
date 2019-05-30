<?php

namespace App\Exceptions\Http;

use App\Exceptions\AppError;

class ValidationError extends AppError
{
  /**
   * Constructor.
   */
  public function __construct()
  {
    parent::__construct(__('http-error.validation'), 422, 'ValidationError');
  }
}
