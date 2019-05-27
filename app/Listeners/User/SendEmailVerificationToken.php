<?php

namespace App\Listeners\User;

use App\Events\User\UserCreatedEvent;
use App\Repositories\Contracts\UserRepository;

class SendEmailVerificationToken
{
  /**
   * @var UserRepository
   */
  private $users;

  /**
   * Create the event listener.
   *
   * @param App\Repositories\Contracts\UserRepository $users
   * @return void
   */
  public function __construct(UserRepository $users)
  {
    $this->users = $users;
  }

  /**
   * Handle the event.
   *
   * @param App\Events\User\UserCreatedEvent $event
   * @return void
   */
  public function handle(UserCreatedEvent $event)
  {
    $user = $event->getUser();
    // TODO:
  }
}
