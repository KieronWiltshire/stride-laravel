<?php

namespace Domain\Menu\Events;

use Domain\Menu\Item;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ItemCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var \Domain\Menu\Item
     */
    private $item;

    /**
     * Create a new event instance.
     *
     * @param \Domain\Menu\Item $item
     */
    public function __construct(Item $item)
    {
        $this->item = $item;
    }

    /**
     * Retrieve the created item.
     *
     * @return \Domain\Menu\Item
     */
    public function item()
    {
        return $this->item;
    }
}
