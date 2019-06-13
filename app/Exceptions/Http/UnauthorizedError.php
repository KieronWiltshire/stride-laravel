<?php

namespace App\Exceptions\Http;

use App\Exceptions\AppError;

class UnauthorizedError extends AppError
{
  /**
   * Create a new unauthorized error instance.
   *
   * @param string $message
   */
  public function __construct($message = null)
  {
    parent::__construct($message ?: __('http.exceptions.unauthorized'), 401, 'UnauthorizedError');
  }
}
