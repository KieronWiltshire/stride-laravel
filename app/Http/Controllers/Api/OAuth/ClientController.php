<?php

namespace App\Http\Controllers\Api\OAuth;

use App\Exceptions\OAuth\ClientNotFoundException;
use App\Repositories\Eloquent\ClientRepository;

class ClientController
{
  /**
   * The client repository instance.
   *
   * @var \App\Repositories\Eloquent\ClientRepository
   */
  protected $clients;

  /**
   * Create a client controller instance.
   *
   * @param \App\Repositories\Eloquent\ClientRepository $clients
   * @return void
   */
  public function __construct(
    ClientRepository $clients
  ) {
    $this->clients = $clients;
  }

  /**
   * Get all of the clients for the authenticated user.
   *
   * @return \Illuminate\Pagination\LengthAwarePaginator<Laravel\Passport\Client>
   */
  public function forUser()
  {
    $userId = request()->user()->getKey();

    $resource = $this->clients->activeForUserAsPaginated($userId, request()->query('limit'), request()->query('offset'));

    $resource->getCollection()->each(function ($client) {
      $client->makeVisible('secret');
    });

    return $resource
      ->setPath(route('api.oauth.clients.index'))
      ->setPageName('offset')
      ->appends([
        'limit' => request()->query('limit')
      ]);
  }

  /**
   * Store a new client.
   *
   * @return \Laravel\Passport\Client
   */
  public function store()
  {
    return $this->clients->create(request()->user()->getKey(), request()->input('name'), request()->input('redirect'))->makeVisible('secret');
  }

  /**
   * Update the given client.
   *
   * @param string $clientId
   * @return \Illuminate\Http\Response|\Laravel\Passport\Client
   */
  public function update($clientId)
  {
    $client = $this->clients->findForUser($clientId, request()->user()->getKey());

    if (!$client) {
      throw (new ClientNotFoundException())->setContext([
        'id' => [
          __('oauth.id.not_found')
        ]
      ]);
    }

    return $this->clients->update($client, request()->input('name'), request()->input('redirect'));
  }

  /**
   * Delete the given client.
   *
   * @param string $clientId
   * @return \Illuminate\Http\Response
   */
  public function destroy($clientId)
  {
    $client = $this->clients->findForUser($clientId, request()->user()->getKey());

    if (!$client || $client->revoked) {
      throw (new ClientNotFoundException())->setContext([
        'id' => [
          __('oauth.id.not_found')
        ]
      ]);
    }

    $this->clients->delete($client);

    return response('', 204);
  }
}