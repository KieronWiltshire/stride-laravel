<?php

namespace Infrastructure\Exceptions\Pagination;

use Infrastructure\Exceptions\Http\BadRequestError;

class InvalidPaginationException extends BadRequestError
{
  /**
   * Create a new invalid pagination exception instance.
   */
  public function __construct() {
    parent::__construct(__('pagination.exceptions.invalid_pagination'));
  }
}
