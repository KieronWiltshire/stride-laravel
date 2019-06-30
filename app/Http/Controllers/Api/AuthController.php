<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Auth\AuthenticationFailedException;
use App\Exceptions\User\UserNotFoundException;
use App\Http\Controllers\Controller;
use App\Contracts\Services\User\UserService;
use App\Transformers\User\UserTransformer;
use Illuminate\Support\Facades\Hash;
use Lcobucci\JWT\Parser;

class AuthController extends Controller
{
  /**
   * @var \App\Contracts\Services\User\UserService
   */
  protected $userService;

  /**
   * @var \App\Transformers\User\UserTransformer
   */
  protected $userTransformer;

  /**
   * Create a new auth controller instance
   *
   * @param \App\Contracts\Services\User\UserService $userService
   * @param \App\Transformers\User\UserTransformer $userTransformer
   */
  public function __construct(
    UserService $userService,
    UserTransformer $userTransformer
  ) {
    $this->userService = $userService;
    $this->userTransformer = $userTransformer;
  }

  /**
   * Retrieve an authentication token.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function login()
  {
    try {
      $user = $this->userService->findByEmail(request()->input('email'));

      if (Hash::check(request()->input('password'), $user->password)) {
        $token = $user->createToken('login', ['*']);

        return response()->json([
          'access_token' => $token->accessToken,
          'token_type' => 'Bearer',
          'expires_at' => $token->token->expires_at
        ]);
      } else {
        throw (new AuthenticationFailedException())->setContext([
          'auth' => [
            __('auth.failed')
          ]
        ]);
      }
    } catch (UserNotFoundException $e) {
        throw $e->setContext([
          'id' => [
          __('user.email.not_found')
          ]
        ]);
    }
  }

  /**
   * Get the authenticated User.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function me()
  {
    return fractal(auth()->user(), $this->userTransformer);
  }

  /**
   * Log the user out (Invalidate the token).
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function logout()
  {
    $jti = (new Parser())->parse(request()->bearerToken())->getHeader('jti');
    $token = auth()->user()->tokens->find($jti);
    $token->revoke();

    return response()->json([
      'message' => __('auth.logout')
    ], 200);
  }
}
