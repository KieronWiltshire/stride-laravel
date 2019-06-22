<?php

namespace App\Transformers;

use Laravel\Passport\Client;
use Illuminate\Support\Facades\Gate;
use League\Fractal\TransformerAbstract;

class ClientTransformer extends TransformerAbstract
{
  /**
   * A Fractal transformer.
   *
   * @return array
   */
  public function transform(Client $client)
  {
    $visible = [];

    if (Gate::allows('client.view', $client)) {
      $visible[] = 'secret';
    }

    return $client->makeVisible($visible)->toArray();
  }
}