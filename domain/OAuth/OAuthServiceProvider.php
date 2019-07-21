<?php

namespace Domain\OAuth;

use Domain\User\User;
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
  }
}
