<?php

namespace App\Validation\Role;

use App\Exceptions\Role\CannotUpdateRoleException;
use App\Validation\AppValidator;

class RoleUpdateValidator extends AppValidator
{
  /**
   * @var \App\Exceptions\AppError
   */
  protected $exception = CannotUpdateRoleException::class;

  /**
   * Retrieve the rules set for the validator.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'name' => 'unique:roles|alpha_dash',
      'display_name' => '',
      'description' => '',
    ];
  }
}