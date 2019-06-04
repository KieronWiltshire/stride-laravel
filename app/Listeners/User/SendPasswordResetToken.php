<?php

namespace App\Listeners\User;

use App\Events\User\PasswordResetTokenGeneratedEvent;
use App\Contracts\User\UserRepositoryInterface;
use Exception;

class SendPasswordResetToken
{
  /**
   * @var \App\Contracts\User\UserRepositoryInterface
   */
  private $users;

  /**
   * Create the event listener.
   *
   * @param \App\Contracts\User\UserRepositoryInterface $users
   * @return void
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
