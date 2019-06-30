<?php

namespace App\Validators\Permission;

use App\Exceptions\Permission\CannotCreatePermissionException;
use App\Validators\AppValidator;

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