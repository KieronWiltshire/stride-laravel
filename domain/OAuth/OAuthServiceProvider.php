<?php

namespace Domain\OAuth;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class OAuthServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   *
   * @return void
   */
  public function register()
  {
    //
  }

  /**
   * Bootstrap services.
   *
   * @return void
   */
  public function boot()
  {
    Gate::define('personal-access-token.for', 'Domain\OAuth\Policies\TokenPolicy@for');
    Gate::define('personal-access-token.create', 'Domain\OAuth\Policies\TokenPolicy@create');
    Gate::define('personal-access-token.delete', 'Domain\OAuth\Policies\TokenPolicy@delete');

    Gate::define('client.for', 'Domain\OAuth\Policies\ClientPolicy@for');
    Gate::define('client.view', 'Domain\OAuth\Policies\ClientPolicy@view');
    Gate::define('client.create', 'Domain\OAuth\Policies\ClientPolicy@create');
    Gate::define('client.update', 'Domain\OAuth\Policies\ClientPolicy@update');
    Gate::define('client.delete', 'Domain\OAuth\Policies\ClientPolicy@delete');

    Gate::after(function ($user, $ability, $result) {
      return ($result && $user->tokenCan($ability));
    });
  }
}
