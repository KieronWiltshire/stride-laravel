<?php

namespace App\Listeners\User;

use App\Events\User\EmailVerificationTokenGeneratedEvent;
use App\Contracts\UserRepositoryInterface;
use Exception;

class SendEmailVerificationToken
{
  /**
   * @var \App\Contracts\UserRepositoryInterface
   */
  private $users;

  /**
   * Create the event listener.
   *
   * @param \App\Contracts\UserRepositoryInterface $users
   */
  public function __construct(UserRepositoryInterface $users)
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
