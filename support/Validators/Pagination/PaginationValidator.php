<?php

namespace Support\Validators\Pagination;

use Support\Exceptions\AppError;
use Support\Exceptions\Pagination\InvalidPaginationException;
use Support\Validators\AppValidator;

class PaginationValidator extends AppValidator
{
    /**
     * @var AppError
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
