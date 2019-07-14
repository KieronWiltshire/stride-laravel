<?php

namespace Domain\Permission;

use Domain\User\User;
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
    Gate::before(function (User $user) {
      if ($user->laratrustCan('permission.*')) {
        return true;
      }
    });

    Gate::define('permission.create', 'App\Policies\Permission\PermissionPolicy@create');
    Gate::define('permission.update', 'App\Policies\Permission\PermissionPolicy@update');
    Gate::define('permission.assign', 'Domain\Permission\Policies\PermissionPolicy@assign');
    Gate::define('permission.deny', 'Domain\Permission\Policies\PermissionPolicy@deny');
  }
}
