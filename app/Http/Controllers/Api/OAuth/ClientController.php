<?php

namespace App\Http\Controllers\Api\OAuth;

use App\Contracts\Repositories\User\UserRepository;
use App\Exceptions\OAuth\ClientNotFoundException;
use App\Exceptions\User\UserNotFoundException;
use App\Http\Controllers\Controller;
use App\Repositories\Client\ClientRepository;
use App\Transformers\Client\ClientTransformer;
use Illuminate\Support\Facades\Gate;

class ClientController extends Controller
{
  /**
   * @var \App\Repositories\Client\ClientRepository
   */
  protected $clientRepository;

  /**
   * @var \App\Contracts\Repositories\User\UserRepository
   */
  protected $userRepository;

  /**
   * @var \App\Transformers\Client\ClientTransformer
   */
  protected $clientTransformer;

  /**
   * Create a client controller instance.
   *
   * @param \App\Repositories\Client\ClientRepository $clientRepository
   * @param \App\Contracts\Repositories\User\UserRepository $userRepository
   * @param \App\Transformers\Client\ClientTransformer $clientTransformer
   */
  public function __construct(
    ClientRepository $clientRepository,
    UserRepository $userRepository,
    ClientTransformer $clientTransformer
  ) {
    $this->clientRepository = $clientRepository;
    $this->userRepository = $userRepository;
    $this->clientTransformer = $clientTransformer;
  }

  /**
   * Get all of the clients for the specified user.
   *
   * @param integer $id
   * @return \Illuminate\Pagination\LengthAwarePaginator<\Laravel\Passport\Client>
   *
   * @throws \App\Exceptions\User\UserNotFoundException
   * @throws \App\Exceptions\Pagination\InvalidPaginationException
   */
  public function forUser($id)
  {
    try {
      $user = $this->userRepository->findById($id);
      $this->authorize('client.for', $user);

      $clients = $this->clientRepository->activeForUserAsPaginated($user->getKey(), request()->query('limit'), request()->query('offset'))
        ->setPath(route('api.oauth.clients.index'))
        ->setPageName('offset')
        ->appends([
          'limit' => request()->query('limit')
        ]);

      return fractal($clients, $this->clientTransformer);
    } catch (UserNotFoundException $e) {
      throw $e->setContext([
        'id' => [
          __('user.id.not_found')
        ]
      ]);
    }
  }

  /**
   * Get all of the clients for the authenticated user.
   *
   * @param integer $id
   * @return \Illuminate\Pagination\LengthAwarePaginator<\Laravel\Passport\Client>
   *
   * @throws \App\Exceptions\User\UserNotFoundException
   * @throws \App\Exceptions\Pagination\InvalidPaginationException
   */
  public function forAuthenticatedUser()
  {
    return $this->forUser(request()->user()->getKey());
  }

  /**
   * Store a new client.
   *
   * @return \Laravel\Passport\Client
   */
  public function store()
  {
    $this->authorize('client.create');

    $client = $this->clientRepository->create(request()->user()->getKey(), request()->input('name'), request()->input('redirect'))->makeVisible('secret');

    return response([], 201)
      ->header('Location', route('api.oauth.client.get', $client->id));
  }

  /**
   * Update the given client.
   *
   * @param string $id
   * @return \Laravel\Passport\Client
   */
  public function update($id)
  {
    $client = $this->clientRepository->findForUser($id, request()->user()->getKey());
    $this->authorize('client.update', $client);

    if (!$client) {
      throw (new ClientNotFoundException())->setContext([
        'id' => [
          __('oauth.id.not_found')
        ]
      ]);
    }

    $client = $this->clientRepository->update($client, request()->input('name'), request()->input('redirect'));

    return fractal($client, $this->clientTransformer);
  }

  /**
   * Delete the given client.
   *
   *
   * @param string $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $client = $this->clientRepository->findForUser($id, request()->user()->getKey());
    $this->authorize('client.delete', $client);

    if ($client->revoked) {
      throw (new ClientNotFoundException())->setContext([
        'id' => [
          __('oauth.client.id.not_found')
        ]
      ]);
    }

    $this->clientRepository->delete($client);

    return response('', 204);
  }
}