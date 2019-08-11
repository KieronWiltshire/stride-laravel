<?php
namespace App\Http\Controllers\Api\OAuth;

use Domain\OAuth\Exceptions\InvalidAuthorizationRequestException;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Contracts\Routing\ResponseFactory;
use Laravel\Passport\Http\Controllers\RetrievesAuthRequestFromSession;

class DenyAuthorizationController
{
  use RetrievesAuthRequestFromSession;

  /**
   * The response factory implementation.
   *
   * @var ResponseFactory
   */
  protected $response;

  /**
   * Create a new controller instance.
   *
   * @param ResponseFactory $response
   */
  public function __construct(
    ResponseFactory $response
  ) {
    $this->response = $response;
  }

  /**
   * Deny the authorization request.
   *
   * @return RedirectResponse
   */
  public function deny()
  {
    try {
      $authRequest = $this->getAuthRequestFromSession();
    } catch (Exception $e) {
      throw new InvalidAuthorizationRequestException();
    }

    $clientUris = Arr::wrap($authRequest->getClient()->getRedirectUri());

    if (! in_array($uri = $authRequest->getRedirectUri(), $clientUris)) {
      $uri = Arr::first($clientUris);
    }

    $separator = $authRequest->getGrantTypeId() === 'implicit' ? '#' : (strstr($uri, '?') ? '&' : '?');

    return $this->response->redirectTo(
      $uri.$separator.'error=access_denied&state=' . request()->input('state')
    );
  }
}
