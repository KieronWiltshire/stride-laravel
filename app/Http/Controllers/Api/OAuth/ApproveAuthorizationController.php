<?php

namespace App\Http\Controllers\Api\OAuth;

use App\Exceptions\OAuth\InvalidAuthorizationRequestException;
use Exception;
use Illuminate\Http\Request;
use Zend\Diactoros\Response as Psr7Response;
use League\OAuth2\Server\AuthorizationServer;
use Laravel\Passport\Http\Controllers\RetrievesAuthRequestFromSession;

class ApproveAuthorizationController
{
  use HandlesOAuthErrors, RetrievesAuthRequestFromSession;

  /**
   * The authorization server.
   *
   * @var \League\OAuth2\Server\AuthorizationServer
   */
  protected $server;

  /**
   * Create a new controller instance.
   *
   * @param \League\OAuth2\Server\AuthorizationServer $server
   * @return void
   */
  public function __construct(
    AuthorizationServer $server
  ) {
    $this->server = $server;
  }

  /**
   * Approve the authorization request.
   *
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Http\Response
   */
  public function approve(Request $request)
  {
    return $this->withErrorHandling(function () use ($request) {
      try {
        $authRequest = $this->getAuthRequestFromSession($request);
      } catch (Exception $e) {
        throw new InvalidAuthorizationRequestException();
      }

      return $this->convertResponse(
        $this->server->completeAuthorizationRequest($authRequest, new Psr7Response)
      );
    });
  }
}
