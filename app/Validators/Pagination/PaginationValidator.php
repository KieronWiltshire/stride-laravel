<?php

namespace App\Validators\Pagination;

use App\Exceptions\Pagination\InvalidPaginationException;
use App\Validators\AppValidator;

class PaginationValidator extends AppValidator
{
  /**
   * @var \App\Exceptions\AppError
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