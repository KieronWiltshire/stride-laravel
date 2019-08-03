<?php

namespace Domain\OAuth\Validators;

use Domain\OAuth\Exceptions\CannotCreateClientException;
use Support\Validators\AppValidator;

class ClientCreateValidator extends AppValidator
{
  /**
   * @var \Support\Exceptions\AppError
   */
  protected $exception = CannotCreateClientException::class;

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