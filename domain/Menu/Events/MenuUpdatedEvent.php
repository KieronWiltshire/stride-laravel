<?php

namespace Domain\Menu\Events;

use Domain\Menu\Menu;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class MenuUpdatedEvent
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  /**
   * @var Menu
   */
  private $menu;

  /**
   * @var array
   */
  private $attributes;

  /**
   * Create a new event instance.
   *
   * @param Menu $menu
   * @param array $attributes
   */
  public function __construct(Menu $menu, $attributes)
  {
    $this->menu = $menu;
    $this->attributes = $attributes;
  }

  /**
   * Retrieve the updated menu.
   *
   * @return Menu
   */
  public function menu()
  {
    return $this->menu;
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
