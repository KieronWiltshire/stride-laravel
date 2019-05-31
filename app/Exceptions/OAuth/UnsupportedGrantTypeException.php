<?php

namespace App\Exceptions\OAuth;

use App\Exceptions\Http\BadRequestError;

class UnsupportedGrantTypeException extends BadRequestError
{
  public function __construct() {
    parent::__construct(__('oauth.exceptions.unsupported_grant_type'));
  }
}
