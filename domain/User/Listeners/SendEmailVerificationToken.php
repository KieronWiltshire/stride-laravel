<?php

namespace Domain\User\Listeners;

use Domain\User\Contracts\Repositories\UserRepository;
use Domain\User\Events\EmailVerificationTokenGeneratedEvent;
use Domain\User\Exceptions\InvalidEmailVerificationTokenException;
use Domain\User\UserService;

class SendEmailVerificationToken
{
    /**
     * @var UserRepository
     */
    private $userService;

    /**
     * Create the event listener.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Handle the event.
     *
     * @param EmailVerificationTokenGeneratedEvent $event
     * @return void
     */
    public function handle(EmailVerificationTokenGeneratedEvent $event)
    {
        try {
            $this->userService->sendEmailVerificationToken($event->user());
        } catch (InvalidEmailVerificationTokenException $e) {
            // log error
        }
    }
}
