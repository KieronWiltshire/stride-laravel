<?php

namespace Domain\OAuth;

use Domain\OAuth\Validators\ClientCreateValidator;
use Domain\OAuth\Validators\ClientUpdateValidator;
use Illuminate\Database\Eloquent\Collection;
use RuntimeException;
use Support\Validators\Pagination\PaginationValidator;
use Illuminate\Pagination\LengthAwarePaginator;
use Laravel\Passport\ClientRepository as PassportClientRepository;
use Domain\OAuth\Exceptions\ClientNotFoundException;
use Laravel\Passport\Client;
use Laravel\Passport\Passport;

/**
 * This repository was created in order to standardize Laravel's Passport
 * with the application's architecture and design philosophy.
 *
 * Class ClientRepository
 * @package App\Repositories
 */
class ClientRepository extends PassportClientRepository
{
    /**
     * @var PaginationValidator
     */
    protected $paginationValidator;

    /**
     * @var ClientCreateValidator
     */
    protected $clientCreateValidator;

    /**
     * @var ClientUpdateValidator
     */
    protected $clientUpdateValidator;

    /**
     * Create a new client repository instance.
     *
     * @param PaginationValidator $paginationValidator
     * @param ClientCreateValidator $clientCreateValidator
     * @param ClientUpdateValidator $clientUpdateValidator
     */
    public function __construct(
        PaginationValidator $paginationValidator,
        ClientCreateValidator $clientCreateValidator,
        ClientUpdateValidator $clientUpdateValidator
    ) {
        $this->paginationValidator = $paginationValidator;
        $this->clientCreateValidator = $clientCreateValidator;
        $this->clientUpdateValidator = $clientUpdateValidator;
    }

    /**
     * Get a client by the given ID.
     *
     * @param int $id
     * @return Client
     *
     * @throws ClientNotFoundException
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
     * @return Client
     *
     * @throws ClientNotFoundException
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
     * @return Client
     *
     * @throws ClientNotFoundException
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
     * @return Collection
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
     * @return LengthAwarePaginator
     *
     * @throws \ReflectionException
     */
    public function forUserAsPaginated($userId, $limit = null, $offset = 1)
    {
        $this->paginationValidator->validate([
            'limit' => $limit,
            'offset' => $offset
        ]);

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
     * @return Collection
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
     * @return LengthAwarePaginator
     *
     * @throws \ReflectionException
     */
    public function activeForUserAsPaginated($userId, $limit = null, $offset = 1)
    {
        $this->paginationValidator->validate([
            'limit' => $limit,
            'offset' => $offset
        ]);

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
     * @return Client
     *
     * @throws \RuntimeException
     * @throws ClientNotFoundException
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
     * @return Client
     *
     * @throws \ReflectionException
     */
    public function create($userId, $name, $redirect, $personalAccess = false, $password = false)
    {
        $this->clientCreateValidator->validate([
            'name' => $name,
            'redirect' => $redirect
        ]);

        return parent::create($userId, $name, $redirect, $personalAccess, $password);
    }

    /**
     * Store a new personal access token client.
     *
     * @param int $userId
     * @param string $name
     * @param string $redirect
     * @return Client
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
     * @return Client
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
     * @return Client
     *
     * @throws \ReflectionException
     */
    public function update(Client $client, $name, $redirect)
    {
        $this->clientUpdateValidator->validate([
            'name' => $name,
            'redirect' => $redirect
        ]);

        return parent::update($client, $name, $redirect);
    }

    /**
     * Regenerate the client secret.
     *
     * @param Client $client
     * @return Client
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
     * @param Client $client
     * @return void
     */
    public function delete(Client $client)
    {
        return parent::delete($client);
    }
}
