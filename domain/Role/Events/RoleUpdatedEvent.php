<?php

namespace Domain\Role\Events;

use Domain\Role\Role;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class RoleUpdatedEvent
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  /**
   * @var \Domain\Role\Role
   */
  private $role;

  /**
   * @var array
   */
  private $attributes;

  /**
   * Create a new event instance.
   *
   * @param \Domain\Role\Role $role
   * @param array $attributes
   */
  public function __construct(Role $role, $attributes)
  {
    $this->role = $role;
    $this->attributes = $attributes;
  }

  /**
   * Retrieve the updated role.
   *
   * @return \Domain\Role\Role
   */
  public function role()
  {
    return $this->role;
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
