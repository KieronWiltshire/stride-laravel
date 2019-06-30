<?php

namespace App\Providers;

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
      'App\Contracts\Repositories\Permission\PermissionRepository',
      'App\Repositories\Permission\PermissionRepository'
    );
    $this->app->bind(
      'App\Contracts\Services\Permission\PermissionService',
      'App\Services\Permission\PermissionService'
    );
  }

  /**
   * Bootstrap services.
   *
   * @return void
   */
  public function boot()
  {
    //
  }
}
