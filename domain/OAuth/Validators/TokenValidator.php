<?php

namespace Domain\OAuth\Validators;

use Laravel\Passport\Passport;
use Support\Validators\AppValidator;

abstract class TokenValidator extends AppValidator
{
    /**
     * @var array
     */
    protected $nameRules = [
    'max:255',
  ];

    /**
     * Retrieve the rules for the scope parameter.
     *
     * @return array
     */
    protected function scopeRules()
    {
        return [
      'array|in:' . implode(',', Passport::scopeIds())
    ];
    }
}
