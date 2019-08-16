<?php

namespace Domain\User\Events;

use Domain\User\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class UserEmailVerifiedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var User
     */
    private $user;

    /**
     * @var string
     */
    private $oldEmail;

    /**
     * Create a new event instance.
     *
     * @param User $user
     */
    public function __construct(User $user, $oldEmail)
    {
        $this->user = $user;
        $this->oldEmail = $oldEmail;
    }

    /**
     * Retrieve the created user.
     *
     * @return User
     */
    public function user()
    {
        return $this->user;
    }

    /**
     * Retrieve the user's old email.
     *
     * @return string
     */
    public function oldEmail()
    {
        return $this->oldEmail;
    }
}
