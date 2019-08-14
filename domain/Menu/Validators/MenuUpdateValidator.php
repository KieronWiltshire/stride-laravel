<?php

namespace Domain\Menu\Validators;

use Domain\Menu\Exceptions\CannotUpdateMenuException;
use Support\Exceptions\AppError;

class MenuUpdateValidator extends MenuValidator
{
    /**
     * @var AppError
     */
    protected $exception = CannotUpdateMenuException::class;

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
