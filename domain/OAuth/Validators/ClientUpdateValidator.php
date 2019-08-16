<?php

namespace Domain\OAuth\Validators;

use Domain\OAuth\Exceptions\CannotUpdateClientException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Support\Exceptions\AppError;

class ClientUpdateValidator extends ClientValidator
{
    /**
     * @var AppError
     */
    protected $exception = CannotUpdateClientException::class;

    /**
     * Retrieve the rules set for the validator.
     *
     * @return array
     * @throws BindingResolutionException
     */
    public function rules()
    {
        return [
            'name' => $this->nameRules,
            'redirect' => $this->redirectRules(),
        ];
    }
}
