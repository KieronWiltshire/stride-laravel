<?php

namespace App\Exceptions\Http;

use App\Exceptions\AppError;

class ValidationError extends AppError
{
  /**
   * Create a new validation error instance.
   *
   * @param string $message
   * @return void
   */
  public function __construct($message = null)
  {
    parent::__construct($message ?: __('http.exceptions.validation'), 422, 'ValidationError');
  }
}
