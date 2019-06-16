<?php

namespace App\Validation\User;

use App\Exceptions\User\CannotCreateUserException;
use App\Validation\AppValidator;

class UserCreateValidator extends AppValidator
{
  /**
   * @var \App\Exceptions\AppError
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