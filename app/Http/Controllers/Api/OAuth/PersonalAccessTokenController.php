<?php

namespace App\Http\Controllers\Api\OAuth;

use App\Http\Controllers\Controller;
use Domain\OAuth\Exceptions\CannotCreateTokenException;
use Domain\OAuth\Exceptions\TokenNotFoundException;
use Domain\OAuth\TokenRepository;
use App\Transformers\TokenTransformer;
use Domain\OAuth\Validators\TokenCreateValidator;
use Domain\User\Exceptions\UserNotFoundException;
use Domain\User\UserService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Response;
use Laravel\Passport\PersonalAccessTokenResult;
use Laravel\Passport\Token;
use Support\Exceptions\AppError;
use Support\Exceptions\Pagination\InvalidPaginationException;

class PersonalAccessTokenController extends Controller
{
    /**
     * @var TokenCreateValidator
     */
    protected $tokenCreateValidator;

    /**
     * @var TokenRepository
     */
    protected $tokenRepository;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var TokenTransformer
     */
    protected $tokenTransformer;

    /**
     * Create a controller instance.
     *
     * @param TokenCreateValidator $tokenCreateValidator
     * @param TokenRepository $tokenRepository
     * @param UserService $userService
     * @param TokenTransformer $tokenTransformer
     */
    public function __construct(
        TokenCreateValidator $tokenCreateValidator,
        TokenRepository $tokenRepository,
        UserService $userService,
        TokenTransformer $tokenTransformer
    ) {
        $this->tokenCreateValidator = $tokenCreateValidator;
        $this->tokenRepository = $tokenRepository;
        $this->userService = $userService;
        $this->tokenTransformer = $tokenTransformer;
    }

    /**
     * Get all of the personal access tokens for the authenticated user.
     *
     * @return LengthAwarePaginator
     *
     * @throws UserNotFoundException
     * @throws InvalidPaginationException
     */
    public function forAuthenticatedUser()
    {
        return $this->forUser(request()->user()->getKey());
    }

    /**
     * Get all of the personal access tokens for the specified user.
     *
     * @param integer $id
     * @return LengthAwarePaginator
     *
     * @throws UserNotFoundException
     * @throws InvalidPaginationException
     */
    public function forUser($id)
    {
        $user = $this->userService->findById($id);
        $this->authorize('personal-access-token.for', $user);

        $tokens = $this->tokenRepository->personalAccessTokensForUserWithClientAndTokenNotRevokedAsPaginated($user->id, request()->input('limit'), request()->input('offset'))
            ->setPath(route('api.oauth.personal-access-tokens.get', $user->id))
            ->setPageName('offset')
            ->appends([
                'limit' => request()->query('limit')
            ]);

        return fractal($tokens, $this->tokenTransformer)->parseIncludes(['client']);
    }

    /**
     * Create a new personal access token for the user.
     *
     * @return PersonalAccessTokenResult
     *
     * @throws AuthorizationException
     * @throws \ReflectionException
     */
    public function store()
    {
        $this->authorize('personal-access-token.create');

        $name = request()->input('name');
        $scopes = request()->input('scopes', []);

        $this->tokenCreateValidator->validate([
            'name' => $name,
            'scopes' => $scopes
        ]);

        return fractal(request()->user()->createToken($name, $scopes), $this->tokenTransformer);
    }

    /**
     * Delete the given token.
     *
     * @param string $tokenId
     * @return Response
     *
     * @throws AuthorizationException
     * @throws TokenNotFoundException
     * @throws AppError
     */
    public function destroy($tokenId)
    {
        $token = $this->tokenRepository->findForUser($tokenId, request()->user()->getKey());
        $this->authorize('personal-access-token.delete', $token);

        if ($token->revoked) {
            throw (new TokenNotFoundException())->setContext([
                'id' => [
                    __('oauth.token.id.not_found')
                ]
            ]);
        }

        $token->revoke();

        return response('', 204);
    }
}
