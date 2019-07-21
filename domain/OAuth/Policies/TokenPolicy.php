<?php

namespace Domain\OAuth\Policies;

use Domain\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Infrastructure\Policies\AppPolicy;
use Laravel\Passport\Token;

class TokenPolicy extends AppPolicy
{
  use HandlesAuthorization;

  /**
   * Determine if the specified user can create a personal access token.
   *
   * @param \Domain\User\User|null $user
   * @return bool
   */
  public function create(?User $user)
  {
    return $this->fallbackToDefault($user, function($subject) use ($user) {
      return (
        $subject->hasPermission('personal-access-token.create')
      );
    });
  }


  /**
   * Determine if the given user can view a given user's personal access tokens.
   *
   * @param \Domain\User\User|null $user
   * @param \Domain\User\User $userToView
   * @return bool
   */
  public function for(?User $user, User $userToView)
  {
    return $this->fallbackToDefault($user, function($subject) use ($user, $userToView) {
      return (
        ($subject->hasPermission('personal-access-token.view.me') && (($user && $userToView) && $user->id === $userToView->id))
        || ($subject->hasPermission('personal-access-token.view.all'))
      );
    });
  }

  /**
   * Determine if the given oauth client can be deleted by the specified user.
   *
   * @param \Domain\User\User|null $user
   * @param \Laravel\Passport\Token $token
   * @return bool
   */
  public function delete(?User $user, Token $token)
  {
    return $this->fallbackToDefault($user, function($subject) use ($user, $token) {
      return (
        ($subject->hasPermission('personal-access-token.delete.me') && (($user && $token) && $user->id === $token->user_id))
        || ($subject->hasPermission('personal-access-token.delete.all'))
      );
    });
  }
}
