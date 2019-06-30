<?php

namespace App\Events\Permission;

use App\Entities\Permission;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class PermissionCreatedEvent
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  /**
   * @var \App\Entities\Permission
   */
  private $permission;

  /**
   * Create a new event instance.
   *
   * @param \App\Entities\Permission $permission
   */
  public function __construct(Permission $permission)
  {
    $this->permission = $permission;
  }

  /**
   * Retrieve the created permission.
   *
   * @return \App\Entities\Permission
   */
  public function permission()
  {
    return $this->permission;
  }
}