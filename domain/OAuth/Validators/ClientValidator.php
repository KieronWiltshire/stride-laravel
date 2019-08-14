<?php

namespace Domain\OAuth\Validators;

use Support\Validators\AppValidator;

abstract class ClientValidator extends AppValidator
{
    /**
     * @var array
     */
    protected $nameRules = [
    'max:255',
  ];

    /**
     * Retrieve's the redirect rules for the client parameter.
     *
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function redirectRules()
    {
        return [
      'required',
      app()->make('\Laravel\Passport\Http\Rules\RedirectRule')
    ];
    }
}
