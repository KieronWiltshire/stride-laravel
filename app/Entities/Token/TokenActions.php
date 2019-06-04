<?php


namespace App\Entities\Token;

use App\Exceptions\OAuth\CannotCreateTokenException;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Laravel\Passport\Passport;

trait TokenActions
{
  /**
   * @var string
   */
  private $nameRules = 'max:255';

  /**
   * Validate the specified parameters for creating a token.
   *
   * @param Illuminate\Contracts\Validation\Factory $validationFactory
   * @param array $attributes
   *
   * @throws \App\Exceptions\OAuth\CannotCreateTokenException
   */
  protected function validateTokenCreateParameters(ValidationFactory $validationFactory, $attributes)
  {
    $validator = $validationFactory->make($attributes, [
      'name' => 'required|' . $this->nameRules,
      'scopes' => 'array|in:' . implode(',', Passport::scopeIds()),
    ]);

    if ($validator->fails()) {
      throw (new CannotCreateTokenException())->setContext($validator->errors()->toArray());
    }
  }
}