<?php

namespace App\Http\Controllers\Api\OAuth;

use App\Contracts\Token\TokenActions;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use App\Repositories\TokenRepository;

class PersonalAccessTokenController
{
  use TokenActions;

  /**
   * The validation factory implementation.
   *
   * @var \Illuminate\Contracts\Validation\Factory
   */
  protected $validation;

  /**
   * The token repository implementation.
   *
   * @var \App\Repositories\TokenRepository
   */
  protected $tokenRepository;

  /**
   * Create a controller instance.
   *
   * @param \Illuminate\Contracts\Validation\Factory $validation
   * @param  \App\Repositories\TokenRepository $tokenRepository
   * @return void
   */
  public function __construct(
    ValidationFactory $validation,
    TokenRepository $tokenRepository
  ) {
    $this->validation = $validation;
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

    $this->validateTokenCreateParameters($this->validation, [
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
    $token->revoke();

    return response('', 204);
  }
}