<?php

namespace Infrastructure\Exceptions\Http;

use Infrastructure\Exceptions\AppError;

class InternalServerError extends AppError
{
  /**
   * Create a new internal server error instance.
   *
   * @param string $message
   */
  public function __construct($message = null)
  {
    parent::__construct($message ?: __('http.exceptions.internal'), 500, 'InternalServerError');
  }
}
