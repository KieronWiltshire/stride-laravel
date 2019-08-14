<?php

namespace Domain\Permission\Events;

use Domain\Permission\Permission;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class PermissionUpdatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Permission
     */
    private $permission;

    /**
     * @var array
     */
    private $attributes;

    /**
     * Create a new event instance.
     *
     * @param Permission $permission
     * @param array $attributes
     */
    public function __construct(Permission $permission, $attributes)
    {
        $this->permission = $permission;
        $this->attributes = $attributes;
    }

    /**
     * Retrieve the updated permission.
     *
     * @return Permission
     */
    public function permission()
    {
        return $this->permission;
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
