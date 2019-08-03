<?php

namespace Support\Exceptions\Http;

use Support\Exceptions\AppError;

class TooManyRequestsError extends AppError
{
  /**
   * Create a new validation error instance.
   *
   * @param string $message
   */
  public function __construct($message = null)
  {
    //        "X-RateLimit-Limit" => 1
    //    "X-RateLimit-Remaining" => 0
    //    "Retry-After" => 58
    //    "X-RateLimit-Reset" => 1560041460
//        dd($exception->getHeaders());

    parent::__construct($message ?: __('http.exceptions.too_many_requests'), 429, 'TooManyRequestsError');
  }
}
