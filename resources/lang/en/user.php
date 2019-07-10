<?php

return [

  /*
  |--------------------------------------------------------------------------
  | User Language Lines
  |--------------------------------------------------------------------------
  |
  | The following language lines are used by the user service.
  |
  */

  'exceptions' => [
    'cannot_create_user' => 'Unable to create the user.',
    'cannot_update_user' => 'Unable to update the user.',
    'invalid_email' => 'The specified email is invalid.',
    'invalid_email_verification_token' => 'The specified email verification token is invalid.',
    'invalid_password' => 'The specified password is invalid.',
    'invalid_password_reset_token' => 'The specified password reset token is invalid.',
    'password_reset_token_expired' => 'The specified password reset token has expired.',
    'not_found' => 'Unable to find the specified user.'
  ],

  'id' => [
    'not_found' => 'We can\'t find a user with the specified identifier.',
  ],

  'email' => [
    'not_found' => 'We can\'t find a user with the specified e-mail address.',
  ],

  'role' => [
    'added' => 'The specified role has been assigned to the user.',
    'removed' => 'The specified role has been removed from the user.'
  ]

];
