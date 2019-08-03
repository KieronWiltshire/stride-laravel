<?php

namespace Domain\Permission;

use Illuminate\Support\ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   *
   * @return void
   */
  public function register()
  {
    $this->app->bind(
      'Domain\Permission\Contracts\Repositories\PermissionRepository',
      'Domain\Permission\PermissionRepository'
    );
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
