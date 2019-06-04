<?php

namespace App\Events\User;

use App\Entities\User\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class EmailVerificationTokenGeneratedEvent
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  /**
   * @var \App\Entities\User\User
   */
  private $user;

  /**
   * Create a new event instance.
   *
   * @param \App\Entities\User\User $user
   * @param string $email
   * @return void
   */
  public function __construct(User $user)
  {
    $this->user = $user;
  }

  /**
   * Retrieve the created user.
   *
   * @return \App\Entities\User\User
   */
  public function getUser()
  {
    return $this->user;
  }
}
