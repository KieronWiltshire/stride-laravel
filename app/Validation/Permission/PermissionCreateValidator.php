<?php

namespace App\Validation\Permission;

use App\Exceptions\Permission\CannotCreatePermissionException;
use App\Validation\AppValidator;

class PermissionCreateValidator extends AppValidator
{
  /**
   * @var \App\Exceptions\AppError
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