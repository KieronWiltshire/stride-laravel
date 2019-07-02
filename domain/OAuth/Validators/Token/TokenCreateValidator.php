<?php

namespace App\Validators\OAuth\Token;

use App\Exceptions\OAuth\CannotCreateTokenException;
use App\Validators\AppValidator;
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