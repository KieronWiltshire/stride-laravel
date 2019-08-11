<?php

namespace App\Http\Controllers\Api\OAuth;

use Domain\OAuth\Exceptions\InvalidAuthorizationRequestException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Zend\Diactoros\Response as Psr7Response;
use League\OAuth2\Server\AuthorizationServer;
use Laravel\Passport\Http\Controllers\RetrievesAuthRequestFromSession;

class ApproveAuthorizationController
{
  use HandlesOAuthErrors, RetrievesAuthRequestFromSession;

  /**
   * The authorization server.
   *
   * @var AuthorizationServer
   */
  protected $server;

  /**
   * Create a new controller instance.
   *
   * @param AuthorizationServer $server
   */
  public function __construct(
    AuthorizationServer $server
  ) {
    $this->server = $server;
  }

  /**
   * Approve the authorization request.
   *
   * @param Request $request
   * @return Response
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
