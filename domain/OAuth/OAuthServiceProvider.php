<?php

namespace Domain\OAuth;

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
  }

  /**
   * Bootstrap services.
   *
   * @return void
   */
  public function boot()
  {
    parent::boot();

    //
  }
}
