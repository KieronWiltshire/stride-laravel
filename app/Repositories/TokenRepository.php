<?php

namespace App\Repositories;

use App\Exceptions\OAuth\TokenNotFoundException;
use App\Validation\OAuth\Token\TokenCreateValidator;
use App\Validation\Pagination\PaginationValidator;
use Laravel\Passport\Token;
use Illuminate\Pagination\LengthAwarePaginator;
use Laravel\Passport\TokenRepository as PassportTokenRepository;
use Laravel\Passport\Passport;

class TokenRepository extends PassportTokenRepository
{
  /**
   * @var \App\Validation\Pagination\PaginationValidator
   */
  protected $paginationValidator;

  /**
   * @var \App\Validation\OAuth\Token\TokenCreateValidator
   */
  protected $tokenCreateValidator;

  /**
   * Create a new token repository instance.
   *
   * @param \App\Validation\Pagination\PaginationValidator $paginationValidator
   * @param \App\Validation\OAuth\Token\TokenCreateValidator $tokenCreateValidator
   * @return void
   */
  public function __construct(
    PaginationValidator $paginationValidator,
    TokenCreateValidator $tokenCreateValidator
  ) {
    $this->paginationValidator = $paginationValidator;
    $this->tokenCreateValidator = $tokenCreateValidator;
  }

  /**
   * Creates a new Access Token.
   *
   * @param array $attributes
   * @return \Laravel\Passport\Token
   *
   * @throws \App\Exceptions\OAuth\CannotCreateTokenException
   */
  public function create($attributes)
  {
    $this->tokenCreateValidator->validate($attributes);
    return parent::create($attributes);
  }

  /**
   * Get a token by the given ID.
   *
   * @param string $id
   * @return \Laravel\Passport\Token
   *
   * @throws \App\Exceptions\OAuth\TokenNotFoundException
   */
  public function find($id)
  {
    $token = parent::find($id);

    if (!$token) {
      throw new TokenNotFoundException();
    }

    return $token;
  }

  /**
   * Get a token by the given user ID and token ID.
   *
   * @param string $id
   * @param int $userId
   * @return \Laravel\Passport\Token
   *
   * @throws \App\Exceptions\OAuth\TokenNotFoundException
   */
  public function findForUser($id, $userId)
  {
    $token = parent::findForUser($id, $userId);

    if (!$token) {
      throw new TokenNotFoundException();
    }

    return $token;
  }

  /**
   * Get the token instances for the given user ID.
   *
   * @param mixed $userId
   * @return \Illuminate\Database\Eloquent\Collection
   */
  public function forUser($userId)
  {
    return parent::forUser($userId);
  }

  /**
   * Get the token instances for the given user ID.
   *
   * @param mixed $userId
   * @param integer $limit
   * @param integer $offset
   * @return \Illuminate\Pagination\LengthAwarePaginator<\Laravel\Passport\Token>
   *
   * @throws \App\Exceptions\Pagination\InvalidPaginationException
   */
  public function forUserAsPaginated($userId, $limit = null, $offset = 1)
  {
    $this->paginationValidator->validate([
      'limit' => $limit,
      'offset' => $offset
    ]);

    $query = Passport::token()
      ->where('user_id', $userId);

    if ($limit) {
      return $query->paginate($limit, ['*'], 'page', $offset);
    } else {
      $tokens = $query->get();

      return new LengthAwarePaginator($tokens->all(), $tokens->count(), max($tokens->count(), 1), 1);
    }
  }

  /**
   * Get the token instances for the given user ID.
   *
   * @param mixed $userId
   * @param integer $limit
   * @param integer $offset
   * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<\Laravel\Passport\Token>
   *
   * @throws \App\Exceptions\Pagination\InvalidPaginationException
   */
  public function personalAccessTokensForUserAsPaginatedWithClientAndTokenNotRevoked($userId, $limit = null, $offset = 1)
  {
    $this->paginationValidator->validate([
      'limit' => $limit,
      'offset' => $offset
    ]);

    $query = Passport::token()
      ->with('client')
      ->where('user_id', $userId)
      ->where('revoked', false)
      ->whereHas('client', function ($subQuery) {
        $subQuery->where('personal_access_client', true);
      });

    if ($limit) {
      return $query->paginate($limit, ['*'], 'page', $offset);
    } else {
      $tokens = $query->get();

      return new LengthAwarePaginator($tokens->all(), $tokens->count(), max($tokens->count(), 1), 1);
    }
  }

  /**
   * Get the token instances for the given user ID.
   *
   * @param mixed $userId
   * @param integer $limit
   * @param integer $offset
   * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<\Laravel\Passport\Token>
   *
   * @throws \App\Exceptions\Pagination\InvalidPaginationException
   */
  public function passwordTokensForUserAsPaginatedWithClientAndTokenNotRevoked($userId, $limit = null, $offset = 1)
  {
    $this->paginationValidator->validate([
      'limit' => $limit,
      'offset' => $offset
    ]);

    $query = Passport::token()
      ->with('client')
      ->where('user_id', $userId)
      ->where('revoked', false)
      ->whereHas('client', function ($subQuery) {
        $subQuery->where('password_client', true);
      });

    if ($limit) {
      return $query->paginate($limit, ['*'], 'page', $offset);
    } else {
      $tokens = $query->get();

      return new LengthAwarePaginator($tokens->all(), $tokens->count(), max($tokens->count(), 1), 1);
    }
  }

  /**
   * Get the token instances for the given user ID.
   *
   * @param mixed $userId
   * @param integer $limit
   * @param integer $offset
   * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<\Laravel\Passport\Token>
   *
   * @throws \App\Exceptions\Pagination\InvalidPaginationException
   */
  public function personalAccessOrPasswordTokensForUserAsPaginatedWithClientAndTokenNotRevoked($userId, $limit = null, $offset = 1)
  {
    $this->paginationValidator->validate([
      'limit' => $limit,
      'offset' => $offset
    ]);

    $query = Passport::token()
      ->with('client')
      ->where('user_id', $userId)
      ->where('revoked', false)
      ->whereHas('client', function ($subQuery) {
        $subQuery->where('personal_access_client', true);
        $subQuery->orWhere('password_client', true);
      });

    if ($limit) {
      return $query->paginate($limit, ['*'], 'page', $offset);
    } else {
      $tokens = $query->get();

      return new LengthAwarePaginator($tokens->all(), $tokens->count(), max($tokens->count(), 1), 1);
    }
  }

  /**
   * Get a valid token instance for the given user and client.
   *
   * @param \Illuminate\Database\Eloquent\Model $user
   * @param \Laravel\Passport\Client $client
   * @return \Laravel\Passport\Token
   *
   * @throws \App\Exceptions\OAuth\TokenNotFoundException
   */
  public function getValidToken($user, $client)
  {
    $token = parent::getValidToken($user, $client);

    if (!$token) {
      throw new TokenNotFoundException();
    }

    return $token;
  }

  /**
   * Store the given token instance.
   *
   * @param \Laravel\Passport\Token $token
   * @return void
   */
  public function save(Token $token)
  {
    parent::save($token);
  }

  /**
   * Revoke an access token.
   *
   * @param string $id
   * @return mixed
   */
  public function revokeAccessToken($id)
  {
    return parent::revokeAccessToken($id);
  }

  /**
   * Check if the access token has been revoked.
   *
   * @param string $id
   *
   * @return bool Return true if this token has been revoked
   */
  public function isAccessTokenRevoked($id)
  {
    return parent::isAccessTokenRevoked($id);
  }

  /**
   * Find a valid token for the given user and client.
   *
   * @param \Illuminate\Database\Eloquent\Model $user
   * @param \Laravel\Passport\Client $client
   * @return \Laravel\Passport\Token
   *
   * @throws \App\Exceptions\OAuth\TokenNotFoundException
   */
  public function findValidToken($user, $client)
  {
    $token = parent::findValidToken($user, $client);

    if (!$token) {
      throw new TokenNotFoundException();
    }

    return $token;
  }
}