<?php

namespace App\Exceptions\Http;

use App\Exceptions\AppError;

class NotFoundError extends AppError
{
  /**
   * Create a new not found error instance.
   *
   * @param string $message
   * @return void
   */
  public function __construct($message = null)
  {
    parent::__construct($message ?: __('http.exceptions.not_found'), 404, 'NotFoundError');
  }
}
