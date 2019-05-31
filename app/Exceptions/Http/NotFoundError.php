<?php

namespace App\Exceptions\Http;

use App\Exceptions\AppError;

class NotFoundError extends AppError
{
  /**
   * Constructor.
   *
   * @param string $message
   */
  public function __construct($message = null)
  {
    parent::__construct($message ?: __('http.exceptions.not_found'), 404, 'NotFoundError');
  }
}
