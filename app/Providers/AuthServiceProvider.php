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
     User::class => UserPolicy::class,
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
      return __('permissions.' . $ability, [], app()->getLocale());
    })->toArray());
  }

  /**
   * Register application authorization gates.
   *
   * @return void
   */
  private function registerGates()
  {
    Gate::define('user.view', 'App\Policies\UserPolicy@view');
    Gate::define('user.update', 'App\Policies\UserPolicy@update');

    Gate::define('personal-access-token.for', 'App\Policies\PersonalAccessTokenPolicy@for');
    Gate::define('personal-access-token.create', 'App\Policies\PersonalAccessTokenPolicy@create');
    Gate::define('personal-access-token.delete', 'App\Policies\PersonalAccessTokenPolicy@delete');

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
