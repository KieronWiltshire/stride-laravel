<?php

namespace App\Policies;

use Domain\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Policies\BasePolicy;
use Laravel\Passport\Client;

class ClientPolicy extends BasePolicy
{
  use HandlesAuthorization;

  /**
   * Determine if the specified user can create an oauth client.
   *
   * @param \Domain\User\User|null $user
   * @return bool
   */
  public function create(?User $user)
  {
    return $this->fallbackToDefault($user, function($subject) use ($user) {
      return (
        $subject->hasPermission('client.create')
      );
    });
  }

  /**
   * Determine if the given user can view a given user's oauth clients.
   *
   * @param \Domain\User\User|null $user
   * @param \Domain\User\User $userToView
   * @return bool
   */
  public function for(?User $user, User $userToView)
  {
    return $this->fallbackToDefault($user, function($subject) use ($user, $userToView) {
      return (
        ($subject->hasPermission('client.view.me') && (($user && $userToView) && $user->id === $userToView->id))
        || ($subject->hasPermission('client.view.all'))
      );
    });
  }

  /**
   * Determine if the given user can view oauth client details.
   *
   * @param \Domain\User\User|null $user
   * @param \Laravel\Passport\Client $client
   * @return bool
   */
  public function view(?User $user, Client $client)
  {
    return $this->fallbackToDefault($user, function($subject) use ($user, $client) {
      return (
        ($subject->hasPermission('client.view.me') && (($user && $client) && $user->id === $client->user_id))
        || ($subject->hasPermission('client.view.all'))
      );
    });
  }

  /**
   * Determine if the given oauth client can be updated by the specified user.
   *
   * @param \Domain\User\User|null $user
   * @param \Laravel\Passport\Client $client
   * @return bool
   */
  public function update(?User $user, Client $client)
  {
    return $this->fallbackToDefault($user, function($subject) use ($user, $client) {
      return (
        ($subject->hasPermission('client.update.me') && (($user && $client) && $user->id === $client->user_id))
        || ($subject->hasPermission('client.update.all'))
      );
    });
  }

  /**
   * Determine if the given oauth client can be deleted by the specified user.
   *
   * @param \Domain\User\User|null $user
   * @param \Laravel\Passport\Client $client
   * @return bool
   */
  public function delete(?User $user, Client $client)
  {
    return $this->fallbackToDefault($user, function($subject) use ($user, $client) {
      return (
        ($subject->hasPermission('client.delete.me') && (($user && $client) && $user->id === $client->user_id))
        || ($subject->hasPermission('client.delete.all'))
      );
    });
  }
}
