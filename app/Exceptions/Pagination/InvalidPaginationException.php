<?php

namespace App\Exceptions\Pagination;

use App\Exceptions\Http\BadRequestError;

class InvalidPaginationException extends BadRequestError
{
  public function __construct() {
    parent::__construct(__('pagination.exceptions.invalid_pagination'));
  }
}
