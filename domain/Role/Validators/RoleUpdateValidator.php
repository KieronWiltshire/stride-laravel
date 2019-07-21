<?php

namespace Domain\Role\Validators;

use \Domain\Role\Exceptions\CannotUpdateRoleException;
use Infrastructure\Validators\AppValidator;

class RoleUpdateValidator extends AppValidator
{
  /**
   * @var \Infrastructure\Exceptions\AppError
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