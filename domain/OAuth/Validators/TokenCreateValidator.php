<?php

namespace Domain\OAuth\Validators;

use Domain\OAuth\Exceptions\CannotCreateTokenException;
use Infrastructure\Validators\AppValidator;
use Laravel\Passport\Passport;

class TokenCreateValidator extends AppValidator
{
  /**
   * @var \Infrastructure\Exceptions\AppError
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
      'name' => 'max:255',
      'scope' => 'array|in:' . implode(',', Passport::scopeIds())
    ];
  }
}