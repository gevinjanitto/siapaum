<?php

namespace App\Listeners;

use App\Models\OtpSuccess;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogSentMessage
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        // Extract email and OTP from the mailable
        // handle call to undefined method getOriginalMessage
        if(!method_exists($event->message, 'getOriginalMessage')) {
            return;
        }
        $mailable = $event->message->getOriginalMessage()->getMailable();
        $email = $mailable->email;
        $otp = $mailable->otp;

        // Save the success details to the database
        OtpSuccess::create([
            'email' => $email,
            'otp' => $otp,
        ]);
    }
}
