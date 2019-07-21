<?php

namespace Infrastructure\Exceptions\Http;

use Infrastructure\Exceptions\AppError;

class BadRequestError extends AppError
{
  /**
   * Create a new bad request error instance.
   *
   * @param string $message
   */
  public function __construct($message = null)
  {
    parent::__construct($message ?: __('http.exceptions.bad_request'), 400, 'BadRequestError');
  }
}
