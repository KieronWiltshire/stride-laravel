<?php

namespace Domain\Permission\Validators;

use Support\Validators\AppValidator;

abstract class PermissionValidator extends AppValidator
{
  /**
   * @var array
   */
  protected $nameRules = [
    'unique:permissions',
  ];

  /**
   * @var array
   */
  protected $displayNameRules = [

  ];

  /**
   * @var array
   */
  protected $descriptionRules = [

  ];
}