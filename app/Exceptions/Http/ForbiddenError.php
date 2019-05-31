<?php

namespace App\Exceptions\Http;

use App\Exceptions\AppError;

class ForbiddenError extends AppError
{
  /**
   * Constructor.
   *
   * @param string $message
   */
  public function __construct($message)
  {
    parent::__construct($message ?: __('http.exceptions.forbidden'), 403, 'ForbiddenError');
  }
}
