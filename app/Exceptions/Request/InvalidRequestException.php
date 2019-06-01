<?php

namespace App\Exceptions\Request;

use App\Exceptions\Http\BadRequestError;

class InvalidRequestException extends BadRequestError
{
  /**
   * Create a new invalid request exception instance.
   * 
   * @return void
   */
  public function __construct() {
    parent::__construct(__('request.exceptions.invalid_request'));
  }
}
