<?php

namespace App\Policies;

use App\Entities\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Laravel\Passport\Client;
use Laravel\Passport\Token;

class PersonalAccessTokenPolicy
{
  use HandlesAuthorization;

  /**
   * Create a new policy instance.
   *
   * @return void
   */
  public function __construct()
  {
    //
  }

  /**
   * Determine if the given user can view a given user's personal access tokens.
   *
   * @param \App\Entities\User $user
   * @param \App\Entities\User $userToView
   * @return bool
   */
  public function for(User $user, User $userToView)
  {
    return (
      ($user->laratrustCan('personal-access-token.view.me') && $user->id === $userToView->id)
      || ($user->laratrustCan('personal-access-token.view.all'))
    );
  }

  /**
   * Determine if the specified user can create a personal access token.
   *
   * @param \App\Entities\User $user
   * @return bool
   */
  public function create(User $user)
  {
    return (
      $user->laratrustCan('personal-access-token.create')
    );
  }

  /**
   * Determine if the given oauth client can be deleted by the specified user.
   *
   * @param \App\Entities\User $user
   * @param \Laravel\Passport\Token $token
   * @return bool
   */
  public function delete(User $user, Token $token)
  {
    return (
      ($user->laratrustCan('personal-access-token.delete.me') && $user->id === $token->user_id)
      || ($user->laratrustCan('personal-access-token.delete.all'))
    );
  }
}
