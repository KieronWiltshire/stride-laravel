<?php

namespace App\Events\Role;

use App\Entities\Role;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class RoleCreatedEvent
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  /**
   * @var \App\Entities\Role
   */
  private $role;

  /**
   * Create a new event instance.
   *
   * @param \App\Entities\Role $role
   */
  public function __construct(Role $role)
  {
    $this->role = $role;
  }

  /**
   * Retrieve the created role.
   *
   * @return \App\Entities\Role
   */
  public function role()
  {
    return $this->role;
  }
}