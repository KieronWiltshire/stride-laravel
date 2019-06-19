<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   *
   * @return void
   */
  public function register()
  {
    $this->app->bind(
      'App\Contracts\Repositories\UserRepository',
      'App\Repositories\UserRepository'
    );
    $this->app->bind(
      'App\Contracts\Repositories\RoleRepository',
      'App\Repositories\RoleRepository'
    );
    $this->app->bind(
      'App\Contracts\Repositories\PermissionRepository',
      'App\Repositories\PermissionRepository'
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
