<?php

namespace App\Exceptions\Pagination;

use App\Exceptions\Http\BadRequestError;

class InvalidPaginationException extends BadRequestError
{
  /**
   * Create a new invalid pagination exception instance.
   * 
   * @return void
   */
  public function __construct() {
    parent::__construct(__('pagination.exceptions.invalid_pagination'));
  }
}
