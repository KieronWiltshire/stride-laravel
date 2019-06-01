<?php

namespace App\Listeners\User;

use App\Events\User\EmailVerificationTokenGeneratedEvent;
use App\Repositories\Contracts\UserRepository;
use Exception;

class SendEmailVerificationToken
{
  /**
   * @var \App\Repositories\Contracts\UserRepository
   */
  private $users;

  /**
   * Create the event listener.
   *
   * @param \App\Repositories\Contracts\UserRepository $users
   * @return void
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
