<?php

namespace Domain\User\Validators;

use App\Exceptions\User\InvalidPasswordException;
use App\Validators\AppValidator;

class UserPasswordValidator extends AppValidator
{
  /**
   * @var \App\Exceptions\AppError
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