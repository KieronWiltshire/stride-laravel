<?php

namespace Domain\Menu\Events;

use Domain\Menu\Item;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ItemUpdatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Item
     */
    private $item;

    /**
     * @var array
     */
    private $attributes;

    /**
     * Create a new event instance.
     *
     * @param Item $item
     * @param array $attributes
     */
    public function __construct(Item $item, $attributes)
    {
        $this->item = $item;
        $this->attributes = $attributes;
    }

    /**
     * Retrieve the updated item.
     *
     * @return Item
     */
    public function item()
    {
        return $this->item;
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
