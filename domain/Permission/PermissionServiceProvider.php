<?php

namespace Domain\Permission;

use Illuminate\Support\ServiceProvider;
use Domain\Permission\Contracts\Repositories\PermissionRepository as PermissionRepositoryInterface;
use Domain\Permission\PermissionRepository;

class PermissionServiceProvider extends ServiceProvider
{
  /**
   * All of the container bindings that should be registered.
   *
   * @var array
   */
  public $bindings = [
    PermissionRepositoryInterface::class => PermissionRepository::class,
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
