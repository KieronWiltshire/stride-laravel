<?php

namespace Domain\Restaurant\Events;

use Domain\Restaurant\Restaurant;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class RestaurantCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var \Domain\Restaurant\Restaurant
     */
    private $restaurant;

    /**
     * Create a new event instance.
     *
     * @param \Domain\Restaurant\Restaurant $restaurant
     */
    public function __construct(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
    }

    /**
     * Retrieve the created restaurant.
     *
     * @return \Domain\Restaurant\Restaurant
     */
    public function restaurant()
    {
        return $this->restaurant;
    }
}
