<?php

namespace Domain\User\Events;

use Domain\User\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class UserEmailVerifiedEvent
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  /**
   * @var \Domain\User\User
   */
  private $user;

  /**
   * @var string
   */
  private $oldEmail;

  /**
   * Create a new event instance.
   *
   * @param \Domain\User\User $user
   */
  public function __construct(User $user, $oldEmail)
  {
    $this->user = $user;
    $this->oldEmail = $oldEmail;
  }

  /**
   * Retrieve the created user.
   * 
   * @return \Domain\User\User
   */
  public function user()
  {
    return $this->user;
  }

  /**
   * Retrieve the user's old email.
   * 
   * @return string
   */
  public function oldEmail()
  {
    return $this->oldEmail;
  }
}
