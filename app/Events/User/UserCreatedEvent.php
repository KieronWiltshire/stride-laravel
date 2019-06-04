<?php

namespace App\Events\User;

use App\Entities\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class UserCreatedEvent
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  /**
   * @var \App\Entities\User
   */
  private $user;

  /**
   * Create a new event instance.
   *
   * @param \App\Entities\User $user
   * @return void
   */
  public function __construct(User $user)
  {
    $this->user = $user;
  }

  /**
   * Retrieve the created user.
   * 
   * @return \App\Entities\User
   */
  public function getUser()
  {
    return $this->user;
  }
}
