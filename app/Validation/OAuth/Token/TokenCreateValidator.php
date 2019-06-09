<?php

namespace App\Validation\OAuth\Token;

use App\Exceptions\OAuth\CannotCreateTokenException;
use App\Validation\AppValidator;
use Laravel\Passport\Passport;

class TokenCreateValidator extends AppValidator
{
  /**
   * @var \App\Exceptions\AppError
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