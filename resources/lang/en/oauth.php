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
    'client_not_found' => 'Unable to find the specified client.',
    'cannot_create_client' => 'Unable to create a client.',
    'cannot_update_client' => 'Unable to update a client.',
    'invalid_authorization_request' => 'The authorization request was not present in the session.'
  ],

  'id' => [
    'not_found' => 'We can\'t find a client with the specified identifier.',
  ]

];
