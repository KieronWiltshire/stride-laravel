<?php

namespace App\Validators\OAuth\Client;

use App\Exceptions\OAuth\CannotUpdateClientException;
use App\Validators\AppValidator;

class ClientUpdateValidator extends AppValidator
{
  /**
   * @var \App\Exceptions\AppError
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