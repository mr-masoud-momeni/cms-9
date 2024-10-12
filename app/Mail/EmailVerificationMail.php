<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $verificationLink;
    public $password;

    public function __construct($verificationLink, $password)
    {
        $this->verificationLink = $verificationLink;
        $this->password = $password;
    }

    public function build()
    {
        return $this->subject('اعتبارسنجی ایمیل و پسورد ورود')
            ->view('emails.verify-email')
            ->with(['verificationLink' => $this->verificationLink, 'password' => $this->password]);
    }
}
