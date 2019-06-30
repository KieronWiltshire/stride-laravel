<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RoleServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   *
   * @return void
   */
  public function register()
  {
    $this->app->bind(
      'App\Contracts\Repositories\Role\RoleRepository',
      'App\Repositories\Role\RoleRepository'
    );
    $this->app->bind(
      'App\Contracts\Services\Role\RoleService',
      'App\Services\Role\RoleService'
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
