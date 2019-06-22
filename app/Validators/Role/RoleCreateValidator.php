<?php

namespace App\Validators\Role;

use App\Exceptions\Role\CannotCreateRoleException;
use App\Validators\AppValidator;

class RoleCreateValidator extends AppValidator
{
  /**
   * @var \App\Exceptions\AppError
   */
  protected $exception = CannotCreateRoleException::class;

  /**
   * Retrieve the rules set for the validator.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'name' => 'required|unique:roles|alpha_dash',
      'display_name' => '',
      'description' => '',
    ];
  }
}