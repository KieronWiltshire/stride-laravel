<?php

namespace App\Exceptions\Http;

use App\Exceptions\AppError;

class ValidationError extends AppError
{
  /**
   * Constructor.
   *
   * @param string $message
   */
  public function __construct($message)
  {
    parent::__construct($message ?: __('http.exceptions.validation'), 422, 'ValidationError');
  }
}
