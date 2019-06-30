<?php

namespace Domain\User\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetToken extends Mailable
{
  use Queueable, SerializesModels;

  /**
   * @var string
   */
  private $passwordResetToken;

  /**
   * Create a new message instance.
   *
   * @param string $to
   * @param string $passwordResetToken
   */
  public function __construct($passwordResetToken)
  {
    $this->passwordResetToken = $passwordResetToken;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this->subject(__('email.subjects.password_reset'))->view('app')->with([
      'passwordResetToken' => $this->passwordResetToken
    ]);
  }
}
