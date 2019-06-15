<?php

namespace App\Http\Controllers\Api\OAuth;

use App\Repositories\TokenRepository;

class AuthorizedAccessTokenController
{
  /**
   * The token repository implementation.
   *
   * @var \App\Repositories\TokenRepository
   */
  protected $tokenRepository;

  /**
   * Create a new controller instance.
   *
   * @param  \App\Repositories\TokenRepository  $tokenRepository
   */
  public function __construct(TokenRepository $tokenRepository)
  {
    $this->tokenRepository = $tokenRepository;
  }

  /**
   * Get all of the authorized tokens for the authenticated user.
   *
   * @return \Illuminate\Database\Eloquent\Collection
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
   * @return \Illuminate\Http\Response
   *
   * @throws \App\Exceptions\OAuth\TokenNotFoundException
   */
  public function destroy($tokenId)
  {
    $token = $this->tokenRepository->findForUser($tokenId, request()->user()->getKey());
    $token->revoke();

    return response('', 204);
  }
}