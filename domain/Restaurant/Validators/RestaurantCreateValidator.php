<?php

namespace Domain\Restaurant\Validators;

use Domain\Restaurant\Exceptions\CannotCreateRestaurantException;
use Support\Exceptions\AppError;

class RestaurantCreateValidator extends RestaurantValidator
{
    /**
     * @var AppError
     */
    protected $exception = CannotCreateRestaurantException::class;

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
