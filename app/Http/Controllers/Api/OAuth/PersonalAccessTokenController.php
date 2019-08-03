<?php

namespace App\Http\Controllers\Api\OAuth;

use App\Http\Controllers\Controller;
use Domain\OAuth\Exceptions\TokenNotFoundException;
use Domain\OAuth\TokenRepository;
use App\Transformers\TokenTransformer;
use Domain\OAuth\Validators\TokenCreateValidator;
use Domain\User\UserService;

class PersonalAccessTokenController extends Controller
{
  /**
   * @var \Domain\OAuth\Validators\TokenCreateValidator
   */
  protected $tokenCreateValidator;

  /**
   * @var \Domain\OAuth\TokenRepository
   */
  protected $tokenRepository;

  /**
   * @var \Domain\User\UserService
   */
  protected $userService;

  /**
   * @var \App\Transformers\TokenTransformer
   */
  protected $tokenTransformer;

  /**
   * Create a controller instance.
   *
   * @param \Domain\OAuth\Validators\TokenCreateValidator $tokenCreateValidator
   * @param \Domain\OAuth\TokenRepository $tokenRepository
   * @param \Domain\User\UserService $userService
   * @param \App\Transformers\TokenTransformer $tokenTransformer
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
   * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<\Laravel\Passport\Token>
   *
   * @throws \Domain\User\Exceptions\UserNotFoundException
   * @throws \Support\Exceptions\Pagination\InvalidPaginationException
   */
  public function forAuthenticatedUser()
  {
    return $this->forUser(request()->user()->getKey());
  }

  /**
   * Get all of the personal access tokens for the specified user.
   *
   * @param integer $id
   * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<\Laravel\Passport\Token>
   *
   * @throws \Domain\User\Exceptions\UserNotFoundException
   * @throws \Support\Exceptions\Pagination\InvalidPaginationException
   */
  public function forUser($id)
  {
    $user = $this->userRepository->findById($id);
//    $this->authorize('personal-access-token.for', $user);

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
   * @return \Laravel\Passport\PersonalAccessTokenResult
   *
   * @throws \Domain\OAuth\Exceptions\CannotCreateTokenException
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
   * @return \Illuminate\Http\Response
   *
   * @throws \Domain\OAuth\Exceptions\TokenNotFoundException
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