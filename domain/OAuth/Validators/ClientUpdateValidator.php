<?php

namespace Domain\OAuth\Validators;

use Domain\OAuth\Exceptions\CannotUpdateClientException;
use Infrastructure\Validators\AppValidator;

class ClientUpdateValidator extends AppValidator
{
  /**
   * @var \Infrastructure\Exceptions\AppError
   */
  protected $exception = CannotUpdateClientException::class;

  /**
   * Retrieve the rules set for the validator.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'name' => 'required|max:255',
      'redirect' => [
        'required',
        app()->make('\Laravel\Passport\Http\Rules\RedirectRule')
      ],
    ];
  }
}