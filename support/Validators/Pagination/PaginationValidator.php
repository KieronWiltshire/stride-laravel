<?php

namespace Support\Validators\Pagination;

use Support\Exceptions\Pagination\InvalidPaginationException;
use Support\Validators\AppValidator;

class PaginationValidator extends AppValidator
{
  /**
   * @var \Support\Exceptions\AppError
   */
  protected $exception = InvalidPaginationException::class;

  /**
   * Retrieve the rules set for the validator.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'limit' => 'nullable|numeric|min:1',
      'offset' => 'nullable|numeric|min:1'
    ];
  }
}