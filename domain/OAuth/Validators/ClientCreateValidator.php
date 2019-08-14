<?php

namespace Domain\OAuth\Validators;

use Domain\OAuth\Exceptions\CannotCreateClientException;

class ClientCreateValidator extends ClientValidator
{
    /**
     * @var \Support\Exceptions\AppError
     */
    protected $exception = CannotCreateClientException::class;

    /**
     * Retrieve the rules set for the validator.
     *
     * @return array
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
