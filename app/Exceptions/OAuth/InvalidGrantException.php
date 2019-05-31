<?php

namespace App\Exceptions\OAuth;

use App\Exceptions\Http\BadRequestError;

class InvalidGrantException extends BadRequestError
{
  public function __construct() {
    parent::__construct(__('oauth.exceptions.invalid_grant'));
  }
}
