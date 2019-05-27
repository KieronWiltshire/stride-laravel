<?php

namespace App\Events\User;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class EmailVerificationTokenGeneratedEvent
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  /**
   * @var App\Entities\User
   */
  private $user;

  /**
   * Create a new event instance.
   *
   * @param App\Entities\User $user
   * @param string $email
   * @return void
   */
  public function __construct($user)
  {
    $this->user = $user;
  }

  /**
   * Retrieve the created user.
   * 
   * @return App\Entities\User
   */
  public function getUser()
  {
    return $this->user;
  }
}
