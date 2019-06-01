<?php

namespace App\Listeners\User;

use App\Events\User\PasswordResetTokenGeneratedEvent;
use App\Repositories\Contracts\UserRepository;
use Exception;

class SendPasswordResetToken
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
   * @param \App\Events\User\PasswordResetTokenGeneratedEvent $event
   * @return void
   */
  public function handle(PasswordResetTokenGeneratedEvent $event)
  {
    $this->users->sendPasswordResetToken($event->getUser());
  }
}
