<?php

namespace Domain\Restaurant\Events;

use Domain\Restaurant\Restaurant;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class RestaurantUpdatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Restaurant
     */
    private $restaurant;

    /**
     * @var array
     */
    private $attributes;

    /**
     * Create a new event instance.
     *
     * @param Restaurant $restaurant
     * @param array $attributes
     */
    public function __construct(Restaurant $restaurant, $attributes)
    {
        $this->restaurant = $restaurant;
        $this->attributes = $attributes;
    }

    /**
     * Retrieve the updated restaurant.
     *
     * @return Restaurant
     */
    public function restaurant()
    {
        return $this->restaurant;
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
