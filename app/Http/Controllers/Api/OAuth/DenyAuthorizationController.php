<?php
namespace App\Http\Controllers\Api\OAuth;

use App\Exceptions\OAuth\InvalidAuthorizationRequestException;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Contracts\Routing\ResponseFactory;
use Laravel\Passport\Http\Controllers\RetrievesAuthRequestFromSession;

class DenyAuthorizationController
{
  use RetrievesAuthRequestFromSession;

  /**
   * The response factory implementation.
   *
   * @var \Illuminate\Contracts\Routing\ResponseFactory
   */
  protected $response;

  /**
   * Create a new controller instance.
   *
   * @param  \Illuminate\Contracts\Routing\ResponseFactory  $response
   * @return void
   */
  public function __construct(
    ResponseFactory $response
  ) {
    $this->response = $response;
  }

  /**
   * Deny the authorization request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function deny(Request $request)
  {
    try {
      $authRequest = $this->getAuthRequestFromSession($request);
    } catch (Exception $e) {
      throw new InvalidAuthorizationRequestException();
    }

    $clientUris = Arr::wrap($authRequest->getClient()->getRedirectUri());

    if (! in_array($uri = $authRequest->getRedirectUri(), $clientUris)) {
      $uri = Arr::first($clientUris);
    }

    $separator = $authRequest->getGrantTypeId() === 'implicit' ? '#' : (strstr($uri, '?') ? '&' : '?');

    return $this->response->redirectTo(
      $uri.$separator.'error=access_denied&state='.$request->input('state')
    );
  }
}