<?php

namespace App\Events\User;

use App\Entities\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class UserEmailVerifiedEvent
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  /**
   * @var \App\Entities\User
   */
  private $user;

  /**
   * @var string
   */
  private $oldEmail;

  /**
   * Create a new event instance.
   *
   * @param \App\Entities\User $user
   */
  public function __construct(User $user, $oldEmail)
  {
    $this->user = $user;
    $this->oldEmail = $oldEmail;
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

  /**
   * Retrieve the user's old email.
   * 
   * @return string
   */
  public function getOldEmail()
  {
    return $this->oldEmail;
  }
}
