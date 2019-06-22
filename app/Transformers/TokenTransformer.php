<?php

namespace App\Transformers;

use Laravel\Passport\Token;
use Illuminate\Support\Facades\Gate;
use League\Fractal\TransformerAbstract;

class TokenTransformer extends TransformerAbstract
{
  /**
   * @var \App\Transformers\ClientTransformer
   */
  protected $clientTransformer;

  /**
   * Create a new token transformer instance
   *
   * @param \App\Transformers\ClientTransformer $clientTransformer
   */
  public function __construct(
    ClientTransformer $clientTransformer
  ) {
    $this->clientTransformer = $clientTransformer;
  }

  /**
   * List of resources possible to include
   *
   * @var array
   */
  protected $availableIncludes = [
    'client'
  ];

  /**
   * A Fractal transformer.
   *
   * @return array
   */
  public function transform(Token $token)
  {
    $visible = [];

    return $token->makeVisible($visible)->toArray();
  }

  /**
   * Include Client.
   *
   * @return \League\Fractal\Resource\Item
   */
  public function includeClient(Token $token)
  {
    return $this->item($token->client, $this->clientTransformer);
  }
}