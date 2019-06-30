<?php

namespace Domain\User\Listeners;

use App\Events\User\PasswordResetTokenGeneratedEvent;
use App\Contracts\Repositories\User\UserRepository;
use Exception;

class SendPasswordResetToken
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
   * @param \App\Events\User\PasswordResetTokenGeneratedEvent $event
   * @return void
   */
  public function handle(PasswordResetTokenGeneratedEvent $event)
  {
    $this->userRepository->sendPasswordResetToken($event->user());
  }
}
