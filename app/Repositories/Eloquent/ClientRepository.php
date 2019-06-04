<?php

namespace App\Repositories\Eloquent;

use App\Exceptions\Pagination\InvalidPaginationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Laravel\Passport\ClientRepository as PassportClientRepository;
use App\Exceptions\OAuth\ClientNotFoundException;
use Laravel\Passport\Client;
use \Laravel\Passport\Http\Rules\RedirectRule;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use App\Exceptions\OAuth\CannotCreateClientException;
use App\Exceptions\OAuth\CannotUpdateClientException;
use Laravel\Passport\Passport;

class ClientRepository extends PassportClientRepository
{
  /**
   * The validation factory implementation.
   *
   * @var \Illuminate\Contracts\Validation\Factory
   */
  protected $validation;

  /**
   * The redirect validation rule.
   *
   * @var \Laravel\Passport\Http\Rules\RedirectRule
   */
  protected $redirectRule;

  /**
   * Create a new user repository instance.
   *
   * @param \Illuminate\Contracts\Validation\Factory $validation
   * @param \Laravel\Passport\Http\Rules\RedirectRule $redirectRule
   * @return void
   */
  public function __construct(
    ValidationFactory $validation,
    RedirectRule $redirectRule
  )
  {
    $this->validation = $validation;
    $this->redirectRule = $redirectRule;
  }

  /**
   * Get a client by the given ID.
   *
   * @param int $id
   * @return \Laravel\Passport\Client
   *
   * @throws \App\Exceptions\OAuth\ClientNotFoundException
   */
  public function find($id)
  {
    $client = parent::find($id);

    if (!$client) {
      throw new ClientNotFoundException();
    }

    return $client;
  }

  /**
   * Get an active client by the given ID.
   *
   * @param int $id
   * @return \Laravel\Passport\Client
   *
   * @throws \App\Exceptions\OAuth\ClientNotFoundException
   */
  public function findActive($id)
  {
    $result = parent::findActive($id);

    if (!$result) {
      throw new ClientNotFoundException();
    }

    return $result;
  }

  /**
   * Get a client instance for the given ID and user ID.
   *
   * @param int $clientId
   * @param mixed $userId
   * @return \Laravel\Passport\Client
   *
   * @throws \App\Exceptions\OAuth\ClientNotFoundException
   */
  public function findForUser($clientId, $userId)
  {
    $client = parent::findForUser($clientId, $userId);

    if (!$client) {
      throw new ClientNotFoundException();
    }

    return $client;
  }

  /**
   * Get the client instances for the given user ID.
   *
   * @param  mixed  $userId
   * @return \Illuminate\Database\Eloquent\Collection
   */
  public function forUser($userId)
  {
    return parent::forUser($userId);
  }

  /**
   * Get the client instances for the given user ID.
   *
   * @param mixed $userId
   * @param integer $limit
   * @param integer $offset
   * @return \Illuminate\Pagination\LengthAwarePaginator<Laravel\Passport\Client>
   *
   * @throws \App\Exceptions\Pagination\InvalidPaginationException
   */
  public function forUserAsPaginated($userId, $limit = null, $offset = 1)
  {
    $validator = $this->validation->make([
      'limit' => $limit,
      'offset' => $offset
    ], [
      'limit' => 'nullable|numeric|min:1',
      'offset' => 'nullable|numeric|min:1',
    ]);

    if ($validator->fails()) {
      throw (new InvalidPaginationException())->setContext($validator->errors()->toArray());
    }

    $query = Passport::client()
      ->where('user_id', $userId)
      ->orderBy('name', 'asc');

    if ($limit) {
      return $query->paginate($limit, ['*'], 'page', $offset);
    } else {
      $clients = $query->get();

      return new LengthAwarePaginator($clients->all(), $clients->count(), max($clients->count(), 1), 1);
    }
  }

  /**
   * Get the active client instances for the given user ID.
   *
   * @param mixed $userId
   * @return \Illuminate\Database\Eloquent\Collection
   */
  public function activeForUser($userId)
  {
    return parent::activeForUser($userId);
  }

  /**
   * Get the active client instances for the given user ID.
   *
   * @param mixed $userId
   * @param integer $limit
   * @param integer $offset
   * @return \Illuminate\Pagination\LengthAwarePaginator<Laravel\Passport\Client>
   *
   * @throws \App\Exceptions\Pagination\InvalidPaginationException
   */
  public function activeForUserAsPaginated($userId, $limit = null, $offset = 1)
  {
    $validator = $this->validation->make([
      'limit' => $limit,
      'offset' => $offset
    ], [
      'limit' => 'nullable|numeric|min:1',
      'offset' => 'nullable|numeric|min:1',
    ]);

    if ($validator->fails()) {
      throw (new InvalidPaginationException())->setContext($validator->errors()->toArray());
    }

    $query = Passport::client()
      ->where('user_id', $userId)
      ->where('revoked', false)
      ->orderBy('name', 'asc');

    if ($limit) {
      return $query->paginate($limit, ['*'], 'page', $offset);
    } else {
      $clients = $query->get();

      return new LengthAwarePaginator($clients->all(), $clients->count(), max($clients->count(), 1), 1);
    }
  }

  /**
   * Get the personal access token client for the application.
   *
   * @return \Laravel\Passport\Client
   *
   * @throws \RuntimeException
   * @throws \App\Exceptions\OAuth\ClientNotFoundException
   */
  public function personalAccessClient()
  {
    try {
      $client = parent::personalAccessClient();
    } catch (RuntimeException $e) {
      throw new ClientNotFoundException();
    }

    if (!$client) {
      throw new ClientNotFoundException();
    }

    return $client;
  }

  /**
   * Store a new client.
   *
   * @param int $userId
   * @param string $name
   * @param string $redirect
   * @param bool $personalAccess
   * @param bool $password
   * @return \Laravel\Passport\Client
   *
   * @throws \App\Exceptions\OAuth\CannotCreateClientException
   */
  public function create($userId, $name, $redirect, $personalAccess = false, $password = false)
  {
    $validator = $this->validation->make([
      'name' => $name,
      'redirect' => $redirect
    ], [
      'name' => 'required|max:255',
      'redirect' => [
        'required',
        $this->redirectRule
      ],
    ]);

    if ($validator->fails()) {
      throw (new CannotCreateClientException())->setContext($validator->errors()->toArray());
    }

    return parent::create($userId, $name, $redirect, $personalAccess, $password);
  }

  /**
   * Store a new personal access token client.
   *
   * @param int $userId
   * @param string $name
   * @param string $redirect
   * @return \Laravel\Passport\Client
   */
  public function createPersonalAccessClient($userId, $name, $redirect)
  {
    return parent::createPersonalAccessClient($userId, $name, $redirect);
  }

  /**
   * Store a new password grant client.
   *
   * @param int $userId
   * @param string $name
   * @param string $redirect
   * @return \Laravel\Passport\Client
   */
  public function createPasswordGrantClient($userId, $name, $redirect)
  {
    return parent::createPasswordGrantClient($userId, $name, $redirect);
  }

  /**
   * Update the given client.
   *
   * @param Client $client
   * @param string $name
   * @param string $redirect
   * @return \Laravel\Passport\Client
   *
   * @throws \App\Exceptions\OAuth\CannotUpdateClientException
   */
  public function update(Client $client, $name, $redirect)
  {
    $validator = $this->validation->make([
      'name' => $name,
      'redirect' => $redirect
    ], [
      'name' => 'required|max:255',
      'redirect' => [
        'required',
        $this->redirectRule
      ],
    ]);

    if ($validator->fails()) {
      throw (new CannotUpdateClientException())->setContext($validator->errors()->toArray());
    }

    return parent::update($client, $name, $redirect);
  }

  /**
   * Regenerate the client secret.
   *
   * @param \Laravel\Passport\Client $client
   * @return \Laravel\Passport\Client
   */
  public function regenerateSecret(Client $client)
  {
    return parent::regenerateSecret($client);
  }

  /**
   * Determine if the given client is revoked.
   *
   * @param int $id
   * @return bool
   */
  public function revoked($id)
  {
    return parent::revoked($id);
  }

  /**
   * Delete the given client.
   *
   * @param \Laravel\Passport\Client $client
   * @return void
   */
  public function delete(Client $client)
  {
    return parent::delete($client);
  }
}