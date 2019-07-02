<?php

namespace Domain\Role\Validators;

use Domain\Role\Exceptions\CannotCreateRoleException;
use Infrastructure\Validators\AppValidator;

class RoleCreateValidator extends AppValidator
{
  /**
   * @var \Infrastructure\Exceptions\AppError
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