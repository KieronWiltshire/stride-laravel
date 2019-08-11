<?php

namespace Domain\User\Validators;

use Domain\User\Exceptions\InvalidEmailException;
use Support\Exceptions\AppError;
use Support\Validators\AppValidator;

class UserEmailValidator extends UserValidator
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
      'email' => array_merge($this->emailRules, [
        'required'
      ]),
    ];
  }
}
