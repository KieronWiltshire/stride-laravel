<?php

namespace Domain\OAuth\Validators;

use Domain\OAuth\Exceptions\CannotUpdateClientException;

class ClientUpdateValidator extends ClientValidator
{
  /**
   * @var \Support\Exceptions\AppError
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
      'name' => $this->nameRules,
      'redirect' => $this->redirectRules(),
    ];
  }
}