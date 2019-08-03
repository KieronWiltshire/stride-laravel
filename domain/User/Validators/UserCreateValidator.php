<?php

namespace Domain\User\Validators;

use Domain\User\Exceptions\CannotCreateUserException;
use Support\Validators\AppValidator;

class UserCreateValidator extends AppValidator
{
  /**
   * @var \Support\Exceptions\AppError
   */
  protected $exception = CannotCreateUserException::class;

  /**
   * Retrieve the rules set for the validator.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'email' => 'required|unique:users|email',
      'password' => 'required|min:6',
    ];
  }
}