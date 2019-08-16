<?php

namespace Domain\Menu\Events;

use Domain\Menu\Menu;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class MenuCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Menu
     */
    private $menu;

    /**
     * Create a new event instance.
     *
     * @param Menu $menu
     */
    public function __construct(Menu $menu)
    {
        $this->menu = $menu;
    }

    /**
     * Retrieve the created menu.
     *
     * @return Menu
     */
    public function menu()
    {
        return $this->menu;
    }
}