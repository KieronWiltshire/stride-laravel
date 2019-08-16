<?php

namespace App\Http\Controllers\Api\OAuth;

use Domain\OAuth\Exceptions\TokenNotFoundException;
use Domain\OAuth\TokenRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class AuthorizedAccessTokenController
{
    /**
     * The token repository implementation.
     *
     * @var TokenRepository
     */
    protected $tokenRepository;

    /**
     * Create a new controller instance.
     *
     * @param TokenRepository $tokenRepository
     */
    public function __construct(
        TokenRepository $tokenRepository
    ) {
        $this->tokenRepository = $tokenRepository;
    }

    /**
     * Get all of the authorized tokens for the authenticated user.
     *
     * @return LengthAwarePaginator
     * @throws \ReflectionException
     */
    public function forUser()
    {
        $userId = request()->user()->getKey();

        return $this->tokenRepository->personalAccessOrPasswordTokensForUserWithClientAndTokenNotRevokedAsPaginated($userId, request()->query('limit'), request()->query('offset'))
            ->setPath(route('api.oauth.tokens.index'))
            ->setPageName('offset')
            ->appends([
                'limit' => request()->query('limit')
            ]);
    }

    /**
     * Delete the given token.
     *
     * @param  string  $tokenId
     * @return Response
     *
     * @throws TokenNotFoundException
     */
    public function destroy($tokenId)
    {
        $token = $this->tokenRepository->findForUser($tokenId, request()->user()->getKey());
        $token->revoke();

        return response('', 204);
    }
}
