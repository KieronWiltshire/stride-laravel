<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Http\UnauthorizedError;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
  /**
   * Get a JWT via given credentials.
   *
   * @param string $email
   * @return Illuminate\Http\Response
   */
  public function login()
  {
    $credentials = [
      'email' => request()->input('email'),
      'password' => request()->input('password')
    ];

    if (!$token = auth()->attempt($credentials)) {
      throw (new UnauthorizedError())->setContext([
        'auth' => [
          __('auth.failed')
        ]
      ]);
    }

    return $this->respondWithToken($token);
  }

  /**
   * Get the authenticated User.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function me()
  {
    return auth()->user();
  }

  /**
   * Log the user out (Invalidate the token).
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function logout()
  {
    auth()->logout();

    return response()->json([
      'message' => __('auth.logout')
    ], 200);
  }

  /**
   * Refresh a token.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function refresh()
  {
    return $this->respondWithToken(auth()->refresh());
  }

  /**
   * Get the token array structure.
   *
   * @param  string $token
   *
   * @return \Illuminate\Http\JsonResponse
   */
  protected function respondWithToken($token)
  {
    return response()->json([
      'access_token' => $token,
      'token_type' => 'bearer',
      'expires_in' => auth()->factory()->getTTL() * 60
    ]);
  }
}