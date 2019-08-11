<?php

namespace App\Policies;

use Domain\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Policies\BasePolicy;
use Laravel\Passport\Token;

class TokenPolicy extends BasePolicy
{
  use HandlesAuthorization;

  /**
   * Determine if the specified user can create a personal access token.
   *
   * @param User|null $user
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
   * @param User|null $user
   * @param User $userToView
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
   * @param User|null $user
   * @param Token $token
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
