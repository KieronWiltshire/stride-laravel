<?php

namespace Domain\OAuth;

use Domain\OAuth\Exceptions\TokenNotFoundException;
use Domain\OAuth\Validators\TokenCreateValidator;
use Domain\User\User;
use Illuminate\Database\Eloquent\Collection;
use Laravel\Passport\Client;
use Support\Validators\Pagination\PaginationValidator;
use Laravel\Passport\Token;
use Illuminate\Pagination\LengthAwarePaginator;
use Laravel\Passport\TokenRepository as PassportTokenRepository;
use Laravel\Passport\Passport;

/**
 * This repository was created in order to standardize Laravel's Passport
 * package with the application's architecture and design philosophy.
 *
 * This repository was made in order to "hack" the response
 * of the "PassportTokenRepository" so that it fits with
 * the application's response.
 *
 * Class TokenRepository
 * @package App\Repositories
 */
class TokenRepository extends PassportTokenRepository
{
    /**
     * @var PaginationValidator
     */
    protected $paginationValidator;

    /**
     * @var TokenCreateValidator
     */
    protected $tokenCreateValidator;

    /**
     * Create a new token repository instance.
     *
     * @param PaginationValidator $paginationValidator
     * @param TokenCreateValidator $tokenCreateValidator
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
     * @return Token
     *
     * @throws \ReflectionException
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
     * @return Token
     *
     * @throws TokenNotFoundException
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
     * @return Token
     *
     * @throws TokenNotFoundException
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
     * @return Collection
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
     * @return LengthAwarePaginator
     *
     * @throws \ReflectionException
     */
    public function personalAccessTokensForUserWithClientAndTokenNotRevokedAsPaginated($userId, $limit = null, $offset = 1)
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
     * @return LengthAwarePaginator
     *
     * @throws \ReflectionException
     */
    public function passwordTokensForUserWithClientAndTokenNotRevokedAsPaginated($userId, $limit = null, $offset = 1)
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
     * @return LengthAwarePaginator
     *
     * @throws \ReflectionException
     */
    public function personalAccessOrPasswordTokensForUserWithClientAndTokenNotRevokedAsPaginated($userId, $limit = null, $offset = 1)
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
     * @param User $user
     * @param Client $client
     * @return Token
     *
     * @throws TokenNotFoundException
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
     * @param Token $token
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
     * @param User $user
     * @param Client $client
     * @return Token
     *
     * @throws TokenNotFoundException
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
