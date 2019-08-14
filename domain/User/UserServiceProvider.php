<?php

namespace Domain\User;

use Domain\User\Events\EmailVerificationTokenGeneratedEvent;
use Domain\User\Events\PasswordResetTokenGeneratedEvent;
use Domain\User\Listeners\SendEmailVerificationToken;
use Domain\User\Listeners\SendPasswordResetToken;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Domain\User\Contracts\Repositories\UserRepository as UserRepositoryInterface;
use Domain\User\UserRepository;

class UserServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
        UserRepositoryInterface::class => UserRepository::class,
    ];

    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        EmailVerificationTokenGeneratedEvent::class => [
            SendEmailVerificationToken::class,
        ],
        PasswordResetTokenGeneratedEvent::class => [
            SendPasswordResetToken::class
        ]
    ];

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
