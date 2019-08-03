<?php

namespace Domain\User\Validators;

use Domain\User\Exceptions\InvalidEmailException;
use Support\Validators\AppValidator;

class UserEmailValidator extends AppValidator
{
  /**
   * @var \Support\Exceptions\AppError
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