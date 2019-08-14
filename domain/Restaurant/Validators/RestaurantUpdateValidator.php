<?php

namespace Domain\Restaurant\Validators;

use Domain\Restaurant\Exceptions\CannotUpdateRestaurantException;
use Support\Exceptions\AppError;

class RestaurantUpdateValidator extends RestaurantValidator
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
