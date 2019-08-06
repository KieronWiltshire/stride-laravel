<?php

namespace Domain\Menu\Validators;

use Domain\Menu\Exceptions\CannotCreateMenuException;

class MenuCreateValidator extends MenuValidator
{
  /**
   * @var \Support\Exceptions\AppError
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