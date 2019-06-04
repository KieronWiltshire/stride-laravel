<?php

namespace App\Events\User;

use App\Entities\User\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class UserUpdatedEvent
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  /**
   * @var \App\Entities\User\User
   */
  private $user;

  /**
   * @var Array
   */
  private $attributes;

  /**
   * Create a new event instance.
   *
   * @param \App\Entities\User\User $user
   * @param Array $attributes
   * @return void
   */
  public function __construct(User $user, $attributes)
  {
    $this->user = $user;
    $this->attributes = $attributes;
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

  /**
   * Retrieve the attributes that were updated.
   * 
   * @return array
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
}
