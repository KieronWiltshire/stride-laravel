<?php

namespace Domain\Menu\Validators;

use Domain\Menu\Exceptions\CannotCreateItemException;
use Support\Exceptions\AppError;

class ItemCreateValidator extends MenuValidator
{
    /**
     * @var AppError
     */
    protected $exception = CannotCreateItemException::class;

    /**
     * Retrieve the rules set for the validator.
     *
     * @return array
     */
    public function rules()
    {
        return [
      '' => ''
    ];
    }
}
