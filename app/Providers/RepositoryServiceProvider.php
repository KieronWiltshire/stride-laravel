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
      'App\Contracts\Repositories\User\UserRepository',
      'App\Repositories\User\UserRepository'
    );
    $this->app->bind(
      'App\Contracts\Repositories\Role\RoleRepository',
      'App\Repositories\Role\RoleRepository'
    );
    $this->app->bind(
      'App\Contracts\Repositories\Permission\PermissionRepository',
      'App\Repositories\Permission\PermissionRepository'
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
