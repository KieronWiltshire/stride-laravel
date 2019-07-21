<?php

namespace Infrastructure\Exceptions\Http;

use Infrastructure\Exceptions\AppError;

class ForbiddenError extends AppError
{
  /**
   * Create a new forbidden error instance.
   *
   * @param string $message
   */
  public function __construct($message = null)
  {
    parent::__construct($message ?: __('http.exceptions.forbidden'), 403, 'ForbiddenError');
  }
}
