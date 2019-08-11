<?php

namespace Domain\Menu\Validators;

use Domain\Menu\Exceptions\CannotCreateMenuException;
use Support\Exceptions\AppError;

class MenuCreateValidator extends MenuValidator
{
  /**
   * @var AppError
   */
  protected $exception = CannotCreateMenuException::class;

  /**
   * Retrieve the rules set for the validator.
   *
   * @return array
   */
  public function rules()
  {
    return [
      '' => ''
    ];
  }
}
