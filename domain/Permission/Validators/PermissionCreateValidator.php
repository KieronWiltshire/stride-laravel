<?php

namespace Domain\Permission\Validators;

use Domain\Permission\Exceptions\CannotCreatePermissionException;
use Support\Validators\AppValidator;

class PermissionCreateValidator extends AppValidator
{
  /**
   * @var \Support\Exceptions\AppError
   */
  protected $exception = CannotCreatePermissionException::class;

  /**
   * Retrieve the rules set for the validator.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'name' => 'required|unique:permissions',
      'display_name' => '',
      'description' => '',
    ];
  }
}