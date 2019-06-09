<?php

namespace App\Exceptions\OAuth;

use App\Exceptions\Http\ValidationError;

class CannotUpdateClientException extends ValidationError
{
  /**
   * Create a new cannot update client exception instance.
   * 
   * @return void
   */
  public function __construct() {
    parent::__construct(__('oauth.exceptions.cannot_update_client'));
  }
}