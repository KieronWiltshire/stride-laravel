<?php

namespace Domain\User;

use Domain\User\Events\EmailVerificationTokenGeneratedEvent;
use Domain\User\Events\PasswordResetTokenGeneratedEvent;
use Domain\User\Listeners\SendEmailVerificationToken;
use Domain\User\Listeners\SendPasswordResetToken;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   *
   * @return void
   */
  public function register()
  {
    $this->app->bind(
      'Domain\User\Contracts\Repositories\UserRepository',
      'Domain\User\UserRepository'
    );

    Event::listen(EmailVerificationTokenGeneratedEvent::class, SendEmailVerificationToken::class);
    Event::listen(PasswordResetTokenGeneratedEvent::class, SendPasswordResetToken::class);
  }

  /**
   * Bootstrap services.
   *
   * @return void
   */
  public function boot()
  {
    Gate::define('user.view', 'Domain\User\Policies\UserPolicy@view');
    Gate::define('user.update', 'Domain\User\Policies\UserPolicy@update');
    Gate::define('user.assign-role', 'Domain\User\Policies\UserPolicy@assignRole');
    Gate::define('user.deny-role', 'Domain\User\Policies\UserPolicy@denyRole');
    Gate::define('user.assign-permission', 'Domain\User\Policies\UserPolicy@assignPermission');
    Gate::define('user.deny-permission', 'Domain\User\Policies\UserPolicy@denyPermission');
  }
}
