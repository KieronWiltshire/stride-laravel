<?php

namespace App\Transformers;

use Laravel\Passport\Token;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class TokenTransformer extends TransformerAbstract
{
    /**
     * @var ClientTransformer
     */
    protected $clientTransformer;

    /**
     * Create a new token transformer instance
     *
     * @param ClientTransformer $clientTransformer
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
    public function transform($token)
    {
        $visible = [];

        return $token->makeVisible($visible)->toArray();
    }

    /**
     * Include Client.
     *
     * @return Item
     */
    public function includeClient($token)
    {
        return $this->item($token->client, $this->clientTransformer);
    }
}
