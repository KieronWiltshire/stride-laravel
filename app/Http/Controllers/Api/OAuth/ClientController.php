<?php

namespace App\Http\Controllers\Api\OAuth;

use App\Contracts\UserRepositoryInterface;
use App\Exceptions\OAuth\ClientNotFoundException;
use App\Exceptions\User\UserNotFoundException;
use App\Http\Controllers\Controller;
use App\Repositories\ClientRepository;
use Illuminate\Support\Facades\Gate;

class ClientController extends Controller
{
  /**
   * @var \App\Repositories\ClientRepository
   */
  protected $clients;

  /**
   * @var \App\Contracts\UserRepositoryInterface
   */
  private $users;

  /**
   * Create a client controller instance.
   *
   * @param \App\Repositories\ClientRepository $clients
   * @param \App\Contracts\UserRepositoryInterface $users
   */
  public function __construct(
    ClientRepository $clients,
    UserRepositoryInterface $users
  ) {
    $this->clients = $clients;
    $this->users = $users;
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
      $user = $this->users->findById($id);
      $this->authorize('client.for', $user);

      $paginated = $this->clients->activeForUserAsPaginated($user->getKey(), request()->query('limit'), request()->query('offset'));

      $paginated->getCollection()->each(function ($client) {
        if (Gate::allows('client.view', $client)) {
          $client->makeVisible([
            'secret'
          ]);
        }
      });

      return $paginated
        ->setPath(route('api.oauth.clients.index'))
        ->setPageName('offset')
        ->appends([
          'limit' => request()->query('limit')
        ]);
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

    return $this->clients->create(request()->user()->getKey(), request()->input('name'), request()->input('redirect'))->makeVisible('secret');
  }

  /**
   * Update the given client.
   *
   * @param string $id
   * @return \Laravel\Passport\Client
   */
  public function update($id)
  {
    $client = $this->clients->findForUser($id, request()->user()->getKey());
    $this->authorize('client.update', $client);

    if (!$client) {
      throw (new ClientNotFoundException())->setContext([
        'id' => [
          __('oauth.id.not_found')
        ]
      ]);
    }

    $client = $this->clients->update($client, request()->input('name'), request()->input('redirect'));

    if (Gate::allows('client.view', $client)) {
      $client->makeVisible([
        'secret'
      ]);
    }

    return $client;
  }

  /**
   * Delete the given client.
   *
   * @param string $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $client = $this->clients->findForUser($id, request()->user()->getKey());
    $this->authorize('client.delete', $client);

    if ($client->revoked) {
      throw (new ClientNotFoundException())->setContext([
        'id' => [
          __('oauth.client.id.not_found')
        ]
      ]);
    }

    $this->clients->delete($client);

    return response('', 204);
  }
}