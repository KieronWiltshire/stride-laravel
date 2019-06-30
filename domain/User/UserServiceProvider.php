<?php

namespace Domain\User;

use Illuminate\Support\Facades\Gate;
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
      'App\User\Contracts\Repositories\UserRepository',
      'App\User\UserRepository'
    );
  }

  /**
   * Bootstrap services.
   *
   * @return void
   */
  public function boot()
  {
    Gate::define('user.view', 'Domain\User\Policies\UserPolicy@view');
    Gate::define('user.update', 'Domain\User\Policies\UserPolicy@update');
  }
}
