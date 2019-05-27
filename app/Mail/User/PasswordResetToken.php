<?php

namespace App\Mail\User;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetToken extends Mailable
{
  use Queueable, SerializesModels;

  /**
   * @var string
   */
  private $to;

  /**
   * @var string
   */
  private $passwordResetToken;

  /**
   * Create a new message instance.
   *
   * @param string $to
   * @param string $passwordResetToken
   * @return void
   */
  public function __construct($to, $passwordResetToken)
  {
    $this->to = $to;
    $this->passwordResetToken = $passwordResetToken;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this->subject(__('email.password_reset'))->view('view.name')->with([
      'to' => $this->to,
      'passwordResetToken' => $this->passwordResetToken
    ]);
  }
}
