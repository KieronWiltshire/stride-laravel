<?php

namespace App\Exceptions\Router;

use App\Exceptions\Http\NotFoundError;

class UnableToLocateRequestRouteException extends NotFoundError
{
  public function __construct() {
    parent::__construct(__('router.exceptions.unable_to_locate_request_route'));
  }
}
