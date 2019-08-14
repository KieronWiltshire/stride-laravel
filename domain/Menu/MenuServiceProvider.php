<?php

namespace Domain\Role;

use Domain\Menu\Contracts\Repositories\ItemRepository as ItemRepositoryInterface;
use Domain\Menu\Contracts\Repositories\MenuRepository as MenuRepositoryInterface;
use Domain\Menu\ItemRepository;
use Domain\Menu\MenuRepository;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
    MenuRepositoryInterface::class => MenuRepository::class,
    ItemRepositoryInterface::class => ItemRepository::class
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
