<?php

namespace App\Contracts\Pagination;

use App\Exceptions\Pagination\InvalidPaginationException;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;

trait PaginationActions
{
  /**
   * @var string
   */
  private $limitRules = 'nullable|numeric|min:1';

  /**
   * @var string
   */
  private $offsetRules = 'nullable|numeric|min:1';

  /**
   * Validate the specified parameters for pagination.
   *
   * @param \Illuminate\Contracts\Validation\Factory $validationFactory
   * @param integer $limit
   * @param integer $offset
   *
   * @throws \App\Exceptions\Pagination\InvalidPaginationException
   */
  protected function validatePaginationParameters(ValidationFactory $validationFactory, $limit, $offset) {
    $validator = $validationFactory->make([
      'limit' => $limit,
      'offset' => $offset
    ], [
      'limit' => $this->limitRules,
      'offset' => $this->offsetRules,
    ]);

    if ($validator->fails()) {
      throw (new InvalidPaginationException())->setContext($validator->errors()->toArray());
    }
  }
}