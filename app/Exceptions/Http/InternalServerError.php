<?php

namespace App\Exceptions\Http;

use App\Exceptions\AppError;

class InternalServerError extends AppError
{
  /**
   * Create a new internal server error instance.
   *
   * @param string $message
   * @return void
   */
  public function __construct($message = null)
  {
    parent::__construct($message ?: __('http.exceptions.internal'), 500, 'InternalServerError');
  }
}
