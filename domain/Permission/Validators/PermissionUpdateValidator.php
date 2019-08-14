<?php

namespace Domain\Permission\Validators;

use Domain\Permission\Exceptions\CannotUpdatePermissionException;

class PermissionUpdateValidator extends PermissionValidator
{
    /**
     * @var \Support\Exceptions\AppError
     */
    protected $exception = CannotUpdatePermissionException::class;

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
