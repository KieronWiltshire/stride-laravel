<?php

namespace Domain\User\Listeners;

use App\Events\User\EmailVerificationTokenGeneratedEvent;
use Domain\User\UserService;

class SendEmailVerificationToken
{
  /**
   * @var \App\Contracts\Repositories\User\UserRepository
   */
  private $userRepository;

  /**
   * Create the event listener.
   *
   * @param \Domain\User\UserService $userService
   */
  public function __construct(UserService $userService)
  {
    $this->userService = $userService;
  }

  /**
   * Handle the event.
   *
   * @param \Domain\User\Events\EmailVerificationTokenGeneratedEvent $event
   * @return void
   */
  public function handle(EmailVerificationTokenGeneratedEvent $event)
  {
    $this->userService->sendEmailVerificationToken($event->user());
  }
}
