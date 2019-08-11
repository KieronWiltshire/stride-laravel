<?php

namespace Domain\User\Events;

use Domain\User\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class EmailVerificationTokenGeneratedEvent
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  /**
   * @var User
   */
  private $user;

  /**
   * Create a new event instance.
   *
   * @param User $user
   * @param string $email
   */
  public function __construct(User $user)
  {
    $this->user = $user;
  }

  /**
   * Retrieve the created user.
   *
   * @return User
   */
  public function user()
  {
    return $this->user;
  }
}
