<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Auth\AuthenticationFailedException;
use App\Exceptions\User\UserNotFoundException;
use App\Http\Controllers\Controller;
use App\Contracts\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Lcobucci\JWT\Parser;

class AuthController extends Controller
{
  /**
   * @var \App\Contracts\Repositories\UserRepository
   */
  private $userRepository;

  /**
   * Create a new auth controller instance
   *
   * @param \App\Contracts\Repositories\UserRepository $userRepository
   */
  public function __construct(
    UserRepository $userRepository
  ) {
    $this->userRepository = $userRepository;
  }

  /**
   * Retrieve an authentication token.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function login()
  {
    try {
      $user = $this->userRepository->findByEmail(request()->input('email'));

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
    return response()->json(auth()->user());
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
