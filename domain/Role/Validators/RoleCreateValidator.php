<?php

namespace Domain\Role\Validators;

use Domain\Role\Exceptions\CannotCreateRoleException;

class RoleCreateValidator extends RoleValidator
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
      'name' => array_merge($this->nameRules, [
        'required',
      ]),
      'display_name' => $this->displayNameRules,
      'description' => $this->descriptionRules,
    ];
  }
}