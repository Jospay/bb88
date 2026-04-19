<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $verificationLink;

    public function __construct($verificationLink)
    {
        $this->verificationLink = $verificationLink;
    }

    public function build()
    {
        return $this->subject('Password Reset Request - BB88')
                    ->html("
                        <h2>Reset Your Password</h2>
                        <p>A password reset request was received for your player account.</p>
                        <p>Please click the button below to verify your identity and create a new password:</p>
                        <a href='{$this->verificationLink}' style='background:#007bff; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>Verify & Create New Password</a>
                        <p>If you did not request this, please ignore this email.</p>
                    ");
    }
}
