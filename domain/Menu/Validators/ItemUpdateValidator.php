<?php

namespace Domain\Menu\Validators;

use Domain\Menu\Exceptions\CannotUpdateItemException;
use Support\Exceptions\AppError;

class ItemUpdateValidator extends MenuValidator
{
  /**
   * @var \Support\Exceptions\AppError
   */
  protected $exception = CannotUpdateItemException::class;

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
