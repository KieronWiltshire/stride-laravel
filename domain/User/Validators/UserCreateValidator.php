<?php

namespace Domain\User\Validators;

use Domain\User\Exceptions\CannotCreateUserException;
use Support\Exceptions\AppError;

class UserCreateValidator extends UserValidator
{
    /**
     * @var AppError
     */
    protected $exception = CannotCreateUserException::class;

    /**
     * Retrieve the rules set for the validator.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => array_merge($this->emailRules, [
                'required'
            ]),
            'password' => array_merge($this->passwordRules, [
                'required'
            ]),
        ];
    }
}
