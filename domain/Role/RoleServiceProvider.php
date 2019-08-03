<?php

namespace Domain\Role;

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
      'Domain\Role\Contracts\Repositories\RoleRepository',
      'Domain\Role\RoleRepository'
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
