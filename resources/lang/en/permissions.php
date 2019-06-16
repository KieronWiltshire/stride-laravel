<?php

use Illuminate\Support\Facades\Gate;

return [

  /*
  |--------------------------------------------------------------------------
  | Permission Language Lines
  |--------------------------------------------------------------------------
  |
  | The following language lines are used during authorization process for various
  | messages that we need to display to the user for such things as OAuth scopes.
  | You are free to modify these language lines according to your application's
  | requirements.
  */

  'user.view' => 'View a user\'s sensitive data.',
  'user.update' => 'Update a user\'s data.',

  'personal-access-token.for' => 'Retrieve personal access tokens for a user.',
  'personal-access-token.create' => 'Create a personal access token for a user.',
  'personal-access-token.delete' => 'Delete a user\'s personal access token.',

  'client.for' => 'Retrieve all of a user\'s registered OAuth clients.',
  'client.view' => 'View an OAuth client\'s sensitive data.',
  'client.create' => 'Create an OAuth client.',
  'client.update' => 'Update an OAuth client\'s data.',
  'client.delete' => 'Delete an OAuth client',

];
