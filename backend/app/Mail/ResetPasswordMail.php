<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $resetURL;

    /**
     * Create a new message instance.
     *
     * @param string $token The password reset token.
     * @param string $resetURL The base URL for the password reset link.
     */
    public function __construct($token, $resetURL)
    {
        $this->token = $token;
        $this->resetURL = $resetURL;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $url = Str::finish($this->resetURL, '/') . $this->token;

        return $this->subject(Lang::get('Reset Password Notification'))
            ->view('emails.reset_password')
            ->with([
                'url' => $url,
                'expireMinutes' => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire'),
            ]);
    }
}
