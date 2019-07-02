<?php

namespace Domain\User\Events;

use Domain\User\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class UserPasswordResetEvent
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  /**
   * @var \Domain\User\User
   */
  private $user;

  /**
   * Create a new event instance.
   *
   * @param \Domain\User\User $user
   */
  public function __construct(User $user)
  {
    $this->user = $user;
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
}
