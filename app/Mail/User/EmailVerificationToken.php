<?php

namespace App\Mail\User;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailVerificationToken extends Mailable
{
  use Queueable, SerializesModels;

  /**
   * @var string
   */
  private $to;

  /**
   * @var string
   */
  private $emailVerificationToken;

  /**
   * Create a new message instance.
   *
   * @param string $to
   * @param string $passwordResetToken
   * @return void
   */
  public function __construct($to, $emailVerificationToken)
  {
    $this->to = $to;
    $this->emailVerificationToken = $emailVerificationToken;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this->subject(__('email.email_verification'))->view('view.name')->with([
      'to' => $this->to,
      'emailVerificationToken' => $this->emailVerificationToken
    ]);
  }
}
