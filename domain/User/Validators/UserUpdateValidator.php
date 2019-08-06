<?php

namespace Domain\User\Validators;

use Domain\User\Exceptions\CannotUpdateUserException;

class UserUpdateValidator extends UserValidator
{
  /**
   * @var \Support\Exceptions\AppError
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
      'email' => $this->emailRules,
      'password' => $this->passwordRules,
    ];
  }
}