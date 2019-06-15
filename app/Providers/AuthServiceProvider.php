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
  }

  /**
   * Register application authorization gates.
   *
   * @return void
   */
  private function registerGates()
  {
    Gate::define('user.view', 'App\Policies\UserPolicy@display');
    Gate::define('user.update', 'App\Policies\UserPolicy@update');
  }
}
