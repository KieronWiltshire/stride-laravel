<?php

namespace Domain\Permission;

use Illuminate\Support\Facades\Gate;
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
    Gate::define('permission.create', 'App\Policies\Permission\PermissionPolicy@create');
    Gate::define('permission.update', 'App\Policies\Permission\PermissionPolicy@update');
  }
}
