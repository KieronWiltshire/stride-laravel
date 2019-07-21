<?php

namespace Domain\User\Validators;

use Domain\User\Exceptions\InvalidEmailException;
use Infrastructure\Validators\AppValidator;

class UserEmailValidator extends AppValidator
{
  /**
   * @var \Infrastructure\Exceptions\AppError
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