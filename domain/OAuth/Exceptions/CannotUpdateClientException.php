<?php

namespace Domain\OAuth\Exceptions;

use Support\Exceptions\Http\ValidationError;

class CannotUpdateClientException extends ValidationError
{
  /**
   * Create a new cannot update client exception instance.
   */
  public function __construct() {
    parent::__construct(__('oauth.exceptions.cannot_update_client'));
  }
}
