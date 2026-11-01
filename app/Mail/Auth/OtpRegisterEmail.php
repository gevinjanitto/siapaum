<?php

namespace App\Mail\Auth;

use App\Models\OtpFailed;
use App\Models\OtpSuccess;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class OtpRegisterEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $otp;
    public $email;
    /**
     * Create a new message instance.
     */
    public function __construct($email, $otp)
    {
        $this->email = $email;
        $this->otp = $otp;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kode OTP Register Email',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.auth.otp-register',
            with: [
                'otp' => $this->otp,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    public function failed(\Exception $exception)
    {
        // Save the failure details to the database
        OtpFailed::create([
            'email' => $this->email,
            'otp' => $this->otp,
            'exception' => $exception->getMessage(),
        ]);
    }

    public function sent($message)
    {
        OtpSuccess::create([
            'email' => $this->email,
            'otp' => $this->otp,
        ]);
    }
}
