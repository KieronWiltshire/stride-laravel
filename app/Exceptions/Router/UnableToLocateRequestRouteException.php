<?php

namespace App\Exceptions\Router;

use App\Exceptions\Http\NotFoundError;

class UnableToLocateRequestRouteException extends NotFoundError
{
  /**
   * Create a new unable to locate request route exception instance.
   */
  public function __construct() {
    parent::__construct(__('router.exceptions.unable_to_locate_request_route'));
  }
}
