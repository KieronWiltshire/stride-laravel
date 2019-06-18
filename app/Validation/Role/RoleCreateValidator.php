<?php

namespace App\Validation\Role;

use App\Exceptions\Role\CannotCreateRoleException;
use App\Validation\AppValidator;

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
//      'email' => 'required|unique:users|email',
//      'password' => 'required|min:6',
    ];
  }
}