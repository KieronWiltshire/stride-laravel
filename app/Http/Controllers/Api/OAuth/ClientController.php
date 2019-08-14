<?php

namespace App\Http\Controllers\Api\OAuth;

use App\Http\Controllers\Controller;
use Domain\OAuth\ClientRepository;
use Domain\OAuth\Exceptions\ClientNotFoundException;
use App\Transformers\ClientTransformer;
use Domain\User\Exceptions\UserNotFoundException;
use Domain\User\UserService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Laravel\Passport\Client;
use Support\Exceptions\AppError;
use Support\Exceptions\Pagination\InvalidPaginationException;

class ClientController extends Controller
{
    /**
     * @var ClientRepository
     */
    protected $clientRepository;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var ClientTransformer
     */
    protected $clientTransformer;

    /**
     * Create a client controller instance.
     *
     * @param ClientRepository $clientRepository
     * @param UserService $userService
     * @param ClientTransformer $clientTransformer
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
     * @return LengthAwarePaginator
     *
     * @throws AuthorizationException
     * @throws \ReflectionException
     * @throws AppError
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
     * @return LengthAwarePaginator
     *
     * @throws AppError
     * @throws AuthorizationException
     * @throws \ReflectionException
     */
    public function forAuthenticatedUser()
    {
        return $this->forUser(request()->user()->getKey());
    }

    /**
     * Store a new client.
     *
     * @return Client
     * @throws AuthorizationException
     * @throws \ReflectionException
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
     * @return Client
     * @throws AppError
     * @throws AuthorizationException
     * @throws ClientNotFoundException
     * @throws \ReflectionException
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
     * @return Response
     * @throws AppError
     * @throws AuthorizationException
     * @throws ClientNotFoundException
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
