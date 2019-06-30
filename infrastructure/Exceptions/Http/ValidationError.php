<?php

namespace Infrastructure\Exceptions\Http;

use Infrastructure\Exceptions\AppError;

class ValidationError extends AppError
{
  /**
   * Create a new validation error instance.
   *
   * @param string $message
   */
  public function __construct($message = null)
  {
    parent::__construct($message ?: __('http.exceptions.validation'), 422, 'ValidationError');
  }
}
