<?php


namespace App\Mail\Auth;


use Illuminate\Mail\Mailable;

class ForgotMail extends Mailable
{
    public $token;
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function build()
    {
        return $this->view('mailables.auth.forgotMail');
    }
}
