<?php

namespace Domain\Permission\Validators;

use Domain\Permission\Exceptions\CannotCreatePermissionException;
use Support\Exceptions\AppError;

class PermissionCreateValidator extends PermissionValidator
{
    /**
     * @var AppError
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
