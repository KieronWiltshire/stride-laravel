<?php

namespace App\Exceptions\Http;

use App\Exceptions\AppError;

class UnauthorizedError extends AppError
{
  /**
   * Constructor.
   *
   * @param string $message
   */
  public function __construct($message)
  {
    parent::__construct($message ?: __('http.exceptions.unauthorized'), 401, 'UnauthorizedError');
  }
}
