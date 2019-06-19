<?php

namespace App\Validation\Permission;

use App\Exceptions\Permission\CannotUpdatePermissionException;
use App\Validation\AppValidator;

class PermissionUpdateValidator extends AppValidator
{
  /**
   * @var \App\Exceptions\AppError
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