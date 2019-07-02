<?php

namespace Domain\Permission\Validators;

use Domain\Permission\Exceptions\CannotCreatePermissionException;
use Infrastructure\Validators\AppValidator;

class PermissionCreateValidator extends AppValidator
{
  /**
   * @var \Infrastructure\Exceptions\AppError
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
      'name' => 'required|unique:permissions|alpha_dash',
      'display_name' => '',
      'description' => '',
    ];
  }
}