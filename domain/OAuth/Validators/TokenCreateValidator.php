<?php

namespace Domain\OAuth\Validators;

use Domain\OAuth\Exceptions\CannotCreateTokenException;
use Support\Exceptions\AppError;

class TokenCreateValidator extends TokenValidator
{
    /**
     * @var AppError
     */
    protected $exception = CannotCreateTokenException::class;

    /**
     * Retrieve the rules set for the validator.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => $this->nameRules,
            'scope' => $this->scopeRules(),
        ];
    }
}
