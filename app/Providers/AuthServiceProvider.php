<?php

namespace App\Providers;

use App\Entities\User;
use App\Policies\UserPolicy;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
  /**
   * The policy mappings for the application.
   *
   * @var array
   */
  protected $policies = [
//     User::class => UserPolicy::class,
  ];

  /**
   * Register any authentication / authorization services.
   *
   * @return void
   */
  public function boot()
  {
    $this->registerPolicies();
    $this->registerGates();

    Passport::enableImplicitGrant();
    Passport::tokensCan(collect(Gate::abilities())->map(function($gate, $ability) {
      return __('gates.' . $ability, [], app()->getLocale());
    })->toArray());
  }

  /**
   * Register application authorization gates.
   *
   * @return void
   */
  private function registerGates()
  {
    Gate::before(function (\Domain\User\User $user, $ability) {
      if ($user->laratrustCan('user.*')) {
        return true;
      }

      $module = explode('.', $ability)[0];

      if ($user->laratrustCan($module . '.*')) {
        return true;
      }
    });

    Gate::define('user.view', 'App\Policies\UserPolicy@view');
    Gate::define('user.update', 'App\Policies\UserPolicy@update');
    Gate::define('user.assign-role', 'App\Policies\UserPolicy@assignRole');
    Gate::define('user.deny-role', 'App\Policies\UserPolicy@denyRole');
    Gate::define('user.assign-permission', 'App\Policies\UserPolicy@assignPermission');
    Gate::define('user.deny-permission', 'App\Policies\UserPolicy@denyPermission');

    Gate::define('role.create', 'App\Policies\RolePolicy@create');
    Gate::define('role.update', 'App\Policies\RolePolicy@update');
    Gate::define('role.assign', 'App\Policies\RolePolicy@assign');
    Gate::define('role.deny', 'App\Policies\RolePolicy@deny');
    Gate::define('role.assign-permission', 'App\Policies\RolePolicy@assignPermission');
    Gate::define('role.deny-permission', 'App\Policies\RolePolicy@denyPermission');

    Gate::define('permission.create', 'App\Policies\Permission\PermissionPolicy@create');
    Gate::define('permission.update', 'App\Policies\Permission\PermissionPolicy@update');
    Gate::define('permission.assign', 'App\Policies\PermissionPolicy@assign');
    Gate::define('permission.deny', 'App\Policies\PermissionPolicy@deny');

    Gate::define('personal-access-token.for', 'App\Policies\TokenPolicy@for');
    Gate::define('personal-access-token.create', 'App\Policies\TokenPolicy@create');
    Gate::define('personal-access-token.delete', 'App\Policies\TokenPolicy@delete');

    Gate::define('client.for', 'App\Policies\ClientPolicy@for');
    Gate::define('client.view', 'App\Policies\ClientPolicy@view');
    Gate::define('client.create', 'App\Policies\ClientPolicy@create');
    Gate::define('client.update', 'App\Policies\ClientPolicy@update');
    Gate::define('client.delete', 'App\Policies\ClientPolicy@delete');

    Gate::after(function ($user, $ability, $result) {
      return ($result && $user->tokenCan($ability));
    });
  }
}
