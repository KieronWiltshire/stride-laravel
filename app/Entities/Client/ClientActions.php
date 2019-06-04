<?php


namespace App\Entities\Client;

use App\Exceptions\OAuth\CannotCreateClientException;
use App\Exceptions\OAuth\CannotUpdateClientException;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;

trait ClientActions
{
  /**
   * @var string
   */
  private $nameRules = 'max:255';

  /**
   * @var string
   */
  private $redirectRuleNamespace = '\Laravel\Passport\Http\Rules\RedirectRule';

  /**
   * Validate the specified parameters for creating a client.
   *
   * @param Illuminate\Contracts\Validation\Factory $validationFactory
   * @param array $attributes
   *
   * @throws \App\Exceptions\OAuth\CannotCreateClientException
   */
  protected function validateClientCreateParameters(ValidationFactory $validationFactory, $attributes)
  {
    $validator = $validationFactory->make($attributes, [
      'name' => 'required|' . $this->nameRules,
      'redirect' => [
        'required',
        app()->make($this->redirectRuleNamespace)
      ],
    ]);

    if ($validator->fails()) {
      throw (new CannotCreateClientException())->setContext($validator->errors()->toArray());
    }
  }

  /**
   * Validate the specified parameters for updating a client.
   *
   * @param Illuminate\Contracts\Validation\Factory $validationFactory
   * @param array $attributes
   *
   * @throws \App\Exceptions\OAuth\CannotUpdateClientException
   */
  protected function validateClientUpdateParameters(ValidationFactory $validationFactory, $attributes)
  {
    $validator = $validationFactory->make($attributes, [
      'name' => 'required|' . $this->nameRules,
      'redirect' => [
        'required',
        app()->make($this->redirectRuleNamespace)
      ],
    ]);

    if ($validator->fails()) {
      throw (new CannotUpdateClientException())->setContext($validator->errors()->toArray());
    }
  }
}