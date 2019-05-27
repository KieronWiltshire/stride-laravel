<?php

namespace App\Providers;

use App\Events\User\EmailVerificationTokenGeneratedEvent;
use App\Events\User\PasswordResetTokenGeneratedEvent;
use App\Listeners\User\SendEmailVerificationToken;
use App\Listeners\User\SendPasswordResetToken;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
  /**
   * The event listener mappings for the application.
   *
   * @var array
   */
  protected $listen = [
    EmailVerificationTokenGeneratedEvent::class => [
      SendEmailVerificationToken::class,
    ],
    PasswordResetTokenGeneratedEvent::class => [
      SendPasswordResetToken::class,
    ]
  ];

  /**
   * Register any events for your application.
   *
   * @return void
   */
  public function boot()
  {
    parent::boot();

    //
  }
}
