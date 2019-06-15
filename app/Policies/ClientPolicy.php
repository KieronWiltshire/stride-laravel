<?php

namespace App\Policies;

use App\Entities\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Laravel\Passport\Client;

class ClientPolicy
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
   * Determine if the given user can view a given user's oauth clients.
   *
   * @param \App\Entities\User $user
   * @param \App\Entities\User $userToView
   * @return bool
   */
  public function for(User $user, User $userToView)
  {
    return (
      ($user->laratrustCan('client.view.me') && $user->id === $userToView->id)
      || ($user->laratrustCan('client.view.all'))
    );
  }

  /**
   * Determine if the given user can view oauth client details.
   *
   * @param \App\Entities\User $user
   * @param \Laravel\Passport\Client $client
   * @return bool
   */
  public function view(User $user, Client $client)
  {
    return (
      ($user->laratrustCan('client.view.me') && $user->id === $client->user_id)
      || ($user->laratrustCan('client.view.all'))
    );
  }

  /**
   * Determine if the specified user can create an oauth client.
   *
   * @param \App\Entities\User $user
   * @param \Laravel\Passport\Client $client
   * @return bool
   */
  public function create(User $user)
  {
    return (
      $user->laratrustCan('client.create')
    );
  }

  /**
   * Determine if the given oauth client can be updated by the specified user.
   *
   * @param \App\Entities\User $user
   * @param \Laravel\Passport\Client $client
   * @return bool
   */
  public function update(User $user, Client $client)
  {
    return (
      ($user->laratrustCan('client.update.me') && $user->id === $client->user_id)
      || ($user->laratrustCan('client.update.all'))
    );
  }

  /**
   * Determine if the given oauth client can be deleted by the specified user.
   *
   * @param \App\Entities\User $user
   * @param \Laravel\Passport\Client $client
   * @return bool
   */
  public function delete(User $user, Client $client)
  {
    return (
      ($user->laratrustCan('client.delete.me') && $user->id === $client->user_id)
      || ($user->laratrustCan('client.delete.all'))
    );
  }
}
