<?php

namespace Domain\Permission\Events;

use Domain\Permission\Permission;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class PermissionCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var \Domain\Permission\Permission
     */
    private $permission;

    /**
     * Create a new event instance.
     *
     * @param \Domain\Permission\Permission $permission
     */
    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
    }

    /**
     * Retrieve the created permission.
     *
     * @return \Domain\Permission\Permission
     */
    public function permission()
    {
        return $this->permission;
    }
}
