<?php

namespace App\Http\Controllers\Api\OAuth;

use App\Contracts\Token\TokenActions;
use App\Exceptions\OAuth\ClientNotFoundException;
use App\Exceptions\OAuth\TokenNotFoundException;
use App\Validation\OAuth\Token\TokenCreateValidator;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use App\Repositories\TokenRepository;

class PersonalAccessTokenController
{
  /**
   * @var \App\Validation\OAuth\Token\TokenCreateValidator
   */
  protected $tokenCreateValidator;

  /**
   * The token repository implementation.
   *
   * @var \App\Repositories\TokenRepository
   */
  protected $tokenRepository;

  /**
   * Create a controller instance.
   *
   * @param \App\Validation\OAuth\Token\TokenCreateValidator $tokenCreateValidator
   * @param  \App\Repositories\TokenRepository $tokenRepository
   * @return void
   */
  public function __construct(
    TokenCreateValidator $tokenCreateValidator,
    TokenRepository $tokenRepository
  ) {
    $this->tokenCreateValidator = $tokenCreateValidator;
    $this->tokenRepository = $tokenRepository;
  }

  /**
   * Get all of the personal access tokens for the authenticated user.
   *
   * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<\Laravel\Passport\Token>
   *
   * @throws \App\Exceptions\Pagination\InvalidPaginationException
   */
  public function forUser()
  {
    return $this->tokenRepository->personalAccessTokensForUserAsPaginatedWithClientAndTokenNotRevoked(request()->user()->getKey(), request()->input('limit'), request()->input('offset'))
      ->setPath(route('api.oauth.personal-access-tokens.index'))
      ->setPageName('offset')
      ->appends([
        'limit' => request()->query('limit')
      ]);
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
    $name = request()->input('name');
    $scopes = request()->input('scopes', []);

    $this->tokenCreateValidator->validate([
      'name' => $name,
      'scopes' => $scopes
    ]);

    return request()->user()->createToken($name, $scopes);
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