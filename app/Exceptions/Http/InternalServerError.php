<?php

namespace App\Exceptions\Http;

use App\Exceptions\AppError;

class InternalServerError extends AppError
{
  /**
   * Constructor.
   *
   * @param string $message
   */
  public function __construct($message)
  {
    parent::__construct($message ?: __('http.exceptions.internal'), 500, 'InternalServerError');
  }
}
