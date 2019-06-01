<?php

namespace App\Exceptions\Http;

use App\Exceptions\AppError;

class BadRequestError extends AppError
{
  /**
   * Create a new bad request error instance.
   *
   * @param string $message
   * @return void
   */
  public function __construct($message = null)
  {
    parent::__construct($message ?: __('http.exceptions.bad_request'), 400, 'BadRequestError');
  }
}
