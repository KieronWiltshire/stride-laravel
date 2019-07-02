<?php

namespace Domain\User\Events;

use Domain\User\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class UserUpdatedEvent
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  /**
   * @var \Domain\User\User
   */
  private $user;

  /**
   * @var array
   */
  private $attributes;

  /**
   * Create a new event instance.
   *
   * @param \Domain\User\User $user
   * @param array $attributes
   */
  public function __construct(User $user, $attributes)
  {
    $this->user = $user;
    $this->attributes = $attributes;
  }

  /**
   * Retrieve the updated user.
   * 
   * @return \Domain\User\User
   */
  public function user()
  {
    return $this->user;
  }

  /**
   * Retrieve the attributes that were updated.
   * 
   * @return array
   */
  public function attributes()
  {
    return $this->attributes;
  }
}
