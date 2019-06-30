<?php

namespace App\User\Validators;

use App\Exceptions\User\InvalidEmailException;
use App\Validators\AppValidator;

class UserEmailValidator extends AppValidator
{
  /**
   * @var \App\Exceptions\AppError
   */
  protected $exception = InvalidEmailException::class;

  /**
   * Retrieve the rules set for the validator.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'email' => 'required|unique:users|email',
    ];
  }
}