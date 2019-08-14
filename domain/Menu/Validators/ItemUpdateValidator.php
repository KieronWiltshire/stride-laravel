<?php

namespace Domain\Menu\Validators;

use Domain\Menu\Exceptions\CannotUpdateRestaurantException;
use Support\Exceptions\AppError;

class ItemUpdateValidator extends MenuValidator
{
    /**
     * @var \Support\Exceptions\AppError
     */
    protected $exception = CannotUpdateRestaurantException::class;

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
