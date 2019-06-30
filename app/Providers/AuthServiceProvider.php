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
    Gate::define('personal-access-token.for', 'App\Policies\Token\TokenPolicy@for');
    Gate::define('personal-access-token.create', 'App\Policies\Token\TokenPolicy@create');
    Gate::define('personal-access-token.delete', 'App\Policies\Token\TokenPolicy@delete');

    Gate::define('client.for', 'App\Policies\Client\ClientPolicy@for');
    Gate::define('client.view', 'App\Policies\Client\ClientPolicy@view');
    Gate::define('client.create', 'App\Policies\Client\ClientPolicy@create');
    Gate::define('client.update', 'App\Policies\Client\ClientPolicy@update');
    Gate::define('client.delete', 'App\Policies\Client\ClientPolicy@delete');

    Gate::define('role.create', 'App\Policies\Role\RolePolicy@create');
    Gate::define('role.update', 'App\Policies\Role\RolePolicy@update');

    Gate::define('permission.create', 'App\Policies\Permission\PermissionPolicy@create');
    Gate::define('permission.update', 'App\Policies\Permission\PermissionPolicy@update');

    Gate::after(function ($user, $ability, $result) {
      return ($result && $user->tokenCan($ability));
    });
  }
}
