<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
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
      'App\Contracts\Services\User\UserService',
      'App\Services\User\UserService'
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
