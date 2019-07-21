<?php

namespace Infrastructure\Exceptions\Http;

use Infrastructure\Exceptions\AppError;

class NotFoundError extends AppError
{
  /**
   * Create a new not found error instance.
   *
   * @param string $message
   */
  public function __construct($message = null)
  {
    parent::__construct($message ?: __('http.exceptions.not_found'), 404, 'NotFoundError');
  }
}
