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
   * @var \Domain\Menu\Item
   */
  private $item;

  /**
   * @var array
   */
  private $attributes;

  /**
   * Create a new event instance.
   *
   * @param \Domain\Menu\Item $item
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
   * @return \Domain\Menu\Item
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
