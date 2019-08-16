<?php

namespace Domain\OAuth\Validators;

use Domain\OAuth\Exceptions\CannotCreateClientException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Support\Exceptions\AppError;

class ClientCreateValidator extends ClientValidator
{
    /**
     * @var AppError
     */
    protected $exception = CannotCreateClientException::class;

    /**
     * Retrieve the rules set for the validator.
     *
     * @return array
     * @throws BindingResolutionException
     */
    public function rules()
    {
        return [
            'name' => array_merge($this->nameRules, [
                'required',
            ]),
            'redirect' => $this->redirectRules(),
        ];
    }
}
