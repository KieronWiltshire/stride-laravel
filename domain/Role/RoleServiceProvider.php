<?php

namespace Domain\Role;

use Domain\Role\Contracts\Repositories\RoleRepository as RoleRepositoryInterface;
use Domain\Role\RoleRepository;
use Illuminate\Support\ServiceProvider;

class RoleServiceProvider extends ServiceProvider
{
  /**
   * All of the container bindings that should be registered.
   *
   * @var array
   */
  public $bindings = [
    RoleRepositoryInterface::class => RoleRepository::class,
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
    parent::boot();

    //
  }
}
