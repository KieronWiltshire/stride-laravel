<?php

namespace App\Listeners\User;

use App\Events\User\EmailVerificationTokenGeneratedEvent;
use App\Contracts\UserRepository;
use Exception;

class SendEmailVerificationToken
{
  /**
   * @var \App\Contracts\UserRepository
   */
  private $users;

  /**
   * Create the event listener.
   *
   * @param \App\Contracts\UserRepository $users
   */
  public function __construct(UserRepository $users)
  {
    $this->users = $users;
  }

  /**
   * Handle the event.
   *
   * @param \App\Events\User\EmailVerificationTokenGeneratedEvent $event
   * @return void
   */
  public function handle(EmailVerificationTokenGeneratedEvent $event)
  {
    $this->users->sendEmailVerificationToken($event->getUser());
  }
}
