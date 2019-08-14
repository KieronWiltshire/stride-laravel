<?php

namespace Domain\User\Listeners;

use Domain\User\Contracts\Repositories\UserRepository;
use Domain\User\Events\PasswordResetTokenGeneratedEvent;

class SendPasswordResetToken
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * Create the event listener.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Handle the event.
     *
     * @param PasswordResetTokenGeneratedEvent $event
     * @return void
     */
    public function handle(PasswordResetTokenGeneratedEvent $event)
    {
        $this->userRepository->sendPasswordResetToken($event->user());
    }
}
