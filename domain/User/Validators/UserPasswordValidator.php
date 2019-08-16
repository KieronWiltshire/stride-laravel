<?php

namespace Domain\User\Validators;

use Domain\User\Exceptions\InvalidPasswordException;
use Support\Exceptions\AppError;

class UserPasswordValidator extends UserValidator
{
    /**
     * @var AppError
     */
    protected $exception = InvalidPasswordException::class;

    /**
     * Retrieve the rules set for the validator.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password' => array_merge($this->passwordRules, [
                'required'
            ]),
        ];
    }
}
