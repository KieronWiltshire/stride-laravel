<?php

namespace App\Validation\User;

use App\Exceptions\User\InvalidEmailException;
use App\Validation\AppValidator;

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