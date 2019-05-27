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
  private $emailVerificationToken;

  /**
   * Create a new message instance.
   *
   * @param string $passwordResetToken
   * @return void
   */
  public function __construct($emailVerificationToken)
  {
    $this->emailVerificationToken = $emailVerificationToken;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this->subject(__('email-subjects.email_verification'))->view('app')->with([
      'emailVerificationToken' => $this->emailVerificationToken
    ]);
  }
}
