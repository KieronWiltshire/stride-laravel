<?php

namespace App\Listeners\User;

use App\Events\User\EmailVerificationTokenGeneratedEvent;
use App\Contracts\Repositories\UserRepository;

class SendEmailVerificationToken
{
  /**
   * @var \App\Contracts\Repositories\UserRepository
   */
  private $userRepository;

  /**
   * Create the event listener.
   *
   * @param \App\Contracts\Repositories\UserRepository $users
   */
  public function __construct(UserRepository $userRepository)
  {
    $this->userRepository = $userRepository;
  }

  /**
   * Handle the event.
   *
   * @param \App\Events\User\EmailVerificationTokenGeneratedEvent $event
   * @return void
   */
  public function handle(EmailVerificationTokenGeneratedEvent $event)
  {
    $this->userRepository->sendEmailVerificationToken($event->user());
  }
}
