<?php

namespace Domain\Permission\Validators;

use Domain\Permission\Exceptions\CannotCreatePermissionException;

class PermissionCreateValidator extends PermissionValidator
{
  /**
   * @var \Support\Exceptions\AppError
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
      'name' => array_merge($this->nameRules, [
        'required',
      ]),
      'display_name' => $this->displayNameRules,
      'description' => $this->descriptionRules,
    ];
  }
}