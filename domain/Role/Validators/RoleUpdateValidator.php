<?php

namespace Domain\Role\Validators;

use Domain\Role\Exceptions\CannotUpdateRoleException;
use Support\Exceptions\AppError;

class RoleUpdateValidator extends RoleValidator
{
    /**
     * @var \Support\Exceptions\AppError
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
      'name' => $this->nameRules,
      'display_name' => $this->displayNameRules,
      'description' => $this->descriptionRules,
    ];
  }
}
