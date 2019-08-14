<?php

namespace Domain\Role\Validators;

use Support\Validators\AppValidator;

abstract class RoleValidator extends AppValidator
{
    /**
     * @var array
     */
    protected $nameRules = [
    'unique:roles',
    'alpha_dash'
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
