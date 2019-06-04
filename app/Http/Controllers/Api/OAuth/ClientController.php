<?php

namespace App\Http\Controllers\Api\OAuth;

use App\Exceptions\OAuth\ClientNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Passport\ClientRepository;

class ClientController
{
  /**
   * The client repository instance.
   *
   * @var \Laravel\Passport\ClientRepository
   */
  protected $clients;

  /**
   * Create a client controller instance.
   *
   * @param \Laravel\Passport\ClientRepository $clients
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
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Database\Eloquent\Collection
   */
  public function forUser(Request $request)
  {
    $userId = $request->user()->getKey();

    $resource = $this->clients->activeForUserAsPaginated($userId, $request->query('limit'), $request->query('offset'));

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
   * @param \Illuminate\Http\Request $request
   * @return \Laravel\Passport\Client
   */
  public function store(Request $request)
  {
    return $this->clients->create(
      $request->user()->getKey(), $request->name, $request->redirect
    )->makeVisible('secret');
  }

  /**
   * Update the given client.
   *
   * @param \Illuminate\Http\Request $request
   * @param string $clientId
   * @return \Illuminate\Http\Response|\Laravel\Passport\Client
   */
  public function update(Request $request, $clientId)
  {
    $client = $this->clients->findForUser($clientId, $request->user()->getKey());

    if (!$client) {
      throw (new ClientNotFoundException())->setContext([
        'id' => [
          __('oauth.id.not_found')
        ]
      ]);
    }

    return $this->clients->update(
      $client, $request->name, $request->redirect
    );
  }

  /**
   * Delete the given client.
   *
   * @param \Illuminate\Http\Request $request
   * @param string $clientId
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request, $clientId)
  {
    $client = $this->clients->findForUser($clientId, $request->user()->getKey());

    if (!$client || $client->revoked) {
      throw (new ClientNotFoundException())->setContext([
        'id' => [
          __('oauth.id.not_found')
        ]
      ]);
    }

    $this->clients->delete($client);

    return new Response('', Response::HTTP_NO_CONTENT);
  }
}