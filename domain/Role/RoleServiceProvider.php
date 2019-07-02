<?php

namespace Domain\Role;

use Illuminate\Support\Facades\Gate;
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
    Gate::define('role.create', 'Domain\Role\Policies\RolePolicy@create');
    Gate::define('role.update', 'Domain\Role\Policies\RolePolicy@update');
  }
}
