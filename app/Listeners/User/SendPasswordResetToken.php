<?php

namespace App\Listeners\User;

use App\Events\User\PasswordResetTokenGeneratedEvent;
use App\Contracts\UserRepositoryInterface;
use Exception;

class SendPasswordResetToken
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
   * @param \App\Events\User\PasswordResetTokenGeneratedEvent $event
   * @return void
   */
  public function handle(PasswordResetTokenGeneratedEvent $event)
  {
    $this->users->sendPasswordResetToken($event->getUser());
  }
}
