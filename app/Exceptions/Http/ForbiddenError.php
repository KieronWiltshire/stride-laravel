<?php

namespace App\Exceptions\Http;

use App\Exceptions\AppError;

class ForbiddenError extends AppError
{
  /**
   * Create a new forbidden error instance.
   *
   * @param string $message
   * @return void
   */
  public function __construct($message = null)
  {
    parent::__construct($message ?: __('http.exceptions.forbidden'), 403, 'ForbiddenError');
  }
}
