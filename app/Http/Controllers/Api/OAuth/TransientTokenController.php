<?php

namespace App\Http\Controllers\Api\OAuth;

use Laravel\Passport\ApiTokenCookieFactory;

class TransientTokenController
{
  /**
   * The cookie factory instance.
   *
   * @var \Laravel\Passport\ApiTokenCookieFactory
   */
  protected $cookieFactory;

  /**
   * Create a new controller instance.
   *
   * @param  \Laravel\Passport\ApiTokenCookieFactory  $cookieFactory
   * @return void
   */
  public function __construct(ApiTokenCookieFactory $cookieFactory)
  {
    $this->cookieFactory = $cookieFactory;
  }

  /**
   * Get a fresh transient token cookie for the authenticated user.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function refresh()
  {
    return response()->json([
      'message' => __('oauth.token_refreshed')
    ])->withCookie($this->cookieFactory->make(
      request()->user()->getKey(), request()->session()->token()
    ));
  }
}