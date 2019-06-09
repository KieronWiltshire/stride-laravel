<?php

namespace App\Validation\User;

use App\Exceptions\User\CannotUpdateUserException;
use App\Validation\AppValidator;

class UserUpdateValidator extends AppValidator
{
  /**
   * @var \App\Exceptions\AppError
   */
  protected $exception = CannotUpdateUserException::class;

  /**
   * Retrieve the rules set for the validator.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'email' => 'unique:users|email',
      'password' => 'min:6',
    ];
  }
}