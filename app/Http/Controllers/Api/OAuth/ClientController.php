<?php

namespace App\Http\Controllers\Api\OAuth;

use App\Http\Controllers\Controller;
use Domain\OAuth\ClientRepository;
use Domain\OAuth\Exceptions\ClientNotFoundException;
use App\Transformers\ClientTransformer;
use Domain\User\Exceptions\UserNotFoundException;
use Domain\User\UserService;

class ClientController extends Controller
{
  /**
   * @var \Domain\OAuth\ClientRepository
   */
  protected $clientRepository;

  /**
   * @var \Domain\User\UserService
   */
  protected $userService;

  /**
   * @var \App\Transformers\ClientTransformer
   */
  protected $clientTransformer;

  /**
   * Create a client controller instance.
   *
   * @param \Domain\OAuth\ClientRepository $clientRepository
   * @param \Domain\User\UserService $userService
   * @param \App\Transformers\ClientTransformer $clientTransformer
   */
  public function __construct(
    ClientRepository $clientRepository,
    UserService $userService,
    ClientTransformer $clientTransformer
  ) {
    $this->clientRepository = $clientRepository;
    $this->userService = $userService;
    $this->clientTransformer = $clientTransformer;
  }

  /**
   * Get all of the clients for the specified user.
   *
   * @param integer $id
   * @return \Illuminate\Pagination\LengthAwarePaginator<\Laravel\Passport\Client>
   *
   * @throws \Domain\User\Exceptions\UserNotFoundException
   * @throws \Support\Exceptions\Pagination\InvalidPaginationException
   */
  public function forUser($id)
  {
    try {
      $user = $this->userService->findById($id);
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
   * @throws \Domain\User\Exceptions\UserNotFoundException
   * @throws \Support\Exceptions\Pagination\InvalidPaginationException
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