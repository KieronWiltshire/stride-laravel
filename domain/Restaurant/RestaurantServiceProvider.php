<?php

namespace Domain\Role;

use Domain\Restaurant\Contracts\Repositories\RestaurantRepository as RestaurantRepositoryInterface;
use Domain\Restaurant\RestaurantRepository;
use Illuminate\Support\ServiceProvider;

class RestaurantServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
    RestaurantRepositoryInterface::class => RestaurantRepository::class,
  ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
