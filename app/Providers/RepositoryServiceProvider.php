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
      'App\Contracts\UserRepository',
      'App\Repositories\UserRepository'
    );
    $this->app->bind(
      'App\Contracts\RoleRepository',
      'App\Repositories\RoleRepository'
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
