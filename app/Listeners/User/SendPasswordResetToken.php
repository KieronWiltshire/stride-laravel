<?php

namespace App\Listeners\User;

use App\Events\User\PasswordResetTokenGeneratedEvent;
use App\Contracts\UserRepository;
use Exception;

class SendPasswordResetToken
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
   * @param \App\Events\User\PasswordResetTokenGeneratedEvent $event
   * @return void
   */
  public function handle(PasswordResetTokenGeneratedEvent $event)
  {
    $this->users->sendPasswordResetToken($event->getUser());
  }
}
