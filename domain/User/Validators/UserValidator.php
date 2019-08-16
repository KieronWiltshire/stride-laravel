<?php

namespace Domain\User\Validators;

use Support\Validators\AppValidator;

abstract class UserValidator extends AppValidator
{
    /**
     * @var array
     */
    protected $emailRules = [
        'unique:users',
        'email'
    ];

    /**
     * @var array
     */
    protected $passwordRules = [

    ];
}
