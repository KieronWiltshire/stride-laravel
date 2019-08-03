<?php

namespace Domain\Role\Validators;

use Domain\Role\Exceptions\CannotCreateRoleException;
use Support\Validators\AppValidator;

class RoleCreateValidator extends AppValidator
{
  /**
   * @var \Support\Exceptions\AppError
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