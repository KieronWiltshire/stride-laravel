<?php

namespace Domain\User\Validators;

use Domain\User\Exceptions\CannotUpdateUserException;
use Infrastructure\Validators\AppValidator;

class UserUpdateValidator extends AppValidator
{
  /**
   * @var \Infrastructure\Exceptions\AppError
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