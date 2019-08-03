<?php

namespace Domain\Permission\Validators;

use Domain\Permission\Exceptions\CannotUpdatePermissionException;
use Support\Validators\AppValidator;

class PermissionUpdateValidator extends AppValidator
{
  /**
   * @var \Support\Exceptions\AppError
   */
  protected $exception = CannotUpdatePermissionException::class;

  /**
   * Retrieve the rules set for the validator.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'name' => 'unique:permissions|alpha_dash',
      'display_name' => '',
      'description' => '',
    ];
  }
}