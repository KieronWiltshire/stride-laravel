<?php

namespace App\Exceptions\Http;

use App\Exceptions\AppError;

class BadRequestError extends AppError
{
  /**
   * Constructor.
   *
   * @param string $message
   */
  public function __construct($message = null)
  {
    parent::__construct($message ?: __('http.exceptions.bad_request'), 400, 'BadRequestError');
  }
}
