<?php

namespace App\Exceptions\OAuth;

use App\Exceptions\Http\ValidationError;

class CannotCreateClientException extends ValidationError
{
  /**
   * Create a new cannot create client exception instance.
   * 
   * @return void
   */
  public function __construct() {
    parent::__construct(__('oauth.exceptions.cannot_create_client'));
  }
}
