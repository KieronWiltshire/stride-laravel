<?php

namespace App\User\Listeners;

use App\Events\User\EmailVerificationTokenGeneratedEvent;
use App\Contracts\Repositories\User\UserRepository;

class SendEmailVerificationToken
{
  /**
   * @var \App\Contracts\Repositories\User\UserRepository
   */
  private $userRepository;

  /**
   * Create the event listener.
   *
   * @param \App\Contracts\Repositories\User\UserRepository $userRepository
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
