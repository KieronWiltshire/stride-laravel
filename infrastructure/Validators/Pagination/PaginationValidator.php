<?php

namespace Infrastructure\Validators\Pagination;

use Infrastructure\Exceptions\Pagination\InvalidPaginationException;
use Infrastructure\Validators\AppValidator;

class PaginationValidator extends AppValidator
{
  /**
   * @var \Infrastructure\Exceptions\AppError
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