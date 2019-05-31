<?php

return [

  /*
  |--------------------------------------------------------------------------
  | OAuth Language Lines
  |--------------------------------------------------------------------------
  |
  | The following language lines are used during authentication for various
  | messages that we need to display to the user. You are free to modify
  | these language lines according to your application's requirements.
  |
  */

  'exceptions' => [
    'invalid_client' => 'Failed to authenticate the client.',
    'invalid_grant' => 'The provided authorization grant (e.g., authorization code, resource owner credentials) or refresh token is invalid, expired, revoked, does not match the redirection URI used in the authorization request, or was issued to another client.',
    'invalid_refresh_token' => 'The specified refresh token is invalid.',
    'invalid_scope' => 'The requested scope is invalid, unknown, or malformed.',
    'unsupported_grant_type' => 'The authorization grant type is not supported by the authorization server.',
  ],

];
