<?php

namespace Domain\User\Validators;

use Domain\User\Exceptions\InvalidPasswordException;
use Support\Validators\AppValidator;

class UserPasswordValidator extends AppValidator
{
  /**
   * @var \Support\Exceptions\AppError
   */
  protected $exception = InvalidPasswordException::class;

  /**
   * Retrieve the rules set for the validator.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'password' => 'required|min:6',
    ];
  }
}