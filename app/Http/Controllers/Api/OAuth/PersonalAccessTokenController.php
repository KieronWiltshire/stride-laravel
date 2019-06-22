<?php

namespace App\Http\Controllers\Api\OAuth;

use App\Contracts\Repositories\UserRepository;
use App\Exceptions\OAuth\TokenNotFoundException;
use App\Http\Controllers\Controller;
use App\Transformers\TokenTransformer;
use App\Validation\OAuth\Token\TokenCreateValidator;
use App\Repositories\TokenRepository;

class PersonalAccessTokenController extends Controller
{
  /**
   * @var \App\Validation\OAuth\Token\TokenCreateValidator
   */
  protected $tokenCreateValidator;

  /**
   * @var \App\Repositories\TokenRepository
   */
  protected $tokenRepository;

  /**
   * @var \App\Contracts\Repositories\UserRepository
   */
  protected $userRepository;

  /**
   * @var \App\Transformers\TokenTransformer
   */
  protected $tokenTransformer;

  /**
   * Create a controller instance.
   *
   * @param \App\Validation\OAuth\Token\TokenCreateValidator $tokenCreateValidator
   * @param \App\Repositories\TokenRepository $tokenRepository
   * @param \App\Contracts\Repositories\UserRepository $userRepository
   * @param \App\Transformers\TokenTransformer $tokenTransformer
   */
  public function __construct(
    TokenCreateValidator $tokenCreateValidator,
    TokenRepository $tokenRepository,
    UserRepository $userRepository,
    TokenTransformer $tokenTransformer
  ) {
    $this->tokenCreateValidator = $tokenCreateValidator;
    $this->tokenRepository = $tokenRepository;
    $this->userRepository = $userRepository;
    $this->tokenTransformer = $tokenTransformer;
  }

  /**
   * Get all of the personal access tokens for the authenticated user.
   *
   * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<\Laravel\Passport\Token>
   *
   * @throws \App\Exceptions\User\UserNotFoundException
   * @throws \App\Exceptions\Pagination\InvalidPaginationException
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
   * @throws \App\Exceptions\User\UserNotFoundException
   * @throws \App\Exceptions\Pagination\InvalidPaginationException
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

    return fractal($tokens, $this->tokenTransformer)->parseIncludes(['client'])->toArray();
  }

  /**
   * Create a new personal access token for the user.
   *
   * @return \Laravel\Passport\PersonalAccessTokenResult
   *
   * @throws \App\Exceptions\OAuth\CannotCreateTokenException
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

    return fractal(request()->user()->createToken($name, $scopes), $this->tokenTransformer)->toArray();
  }

  /**
   * Delete the given token.
   *
   * @param string $tokenId
   * @return \Illuminate\Http\Response
   *
   * @throws \App\Exceptions\OAuth\TokenNotFoundException
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