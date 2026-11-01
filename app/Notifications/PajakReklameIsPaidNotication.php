<?php

namespace App\Notifications;

use App\Enums\JenisVerifikatorEnum;
use App\Models\Permohonan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PajakReklameIsPaidNotication extends Notification implements ShouldQueue
{
    use Queueable;

    protected Permohonan $permohonan;
    protected $verifikator;

    /**
     * Create a new notification instance.
     */
    public function __construct(Permohonan $permohonan)
    {
        $this->permohonan = $permohonan;
        $this->verifikator = $permohonan->alurPermohonan()->where('jenis_verifikator', JenisVerifikatorEnum::OPD->value)->first()?->verifikator;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail_message = (new MailMessage)
            ->subject('Verifikasi Pembayaran Pajak SKPD Reklame')
            ->markdown(
                'emails.pajak-reklame-is-paid',
                [
                    'permohonan' => $this->permohonan,
                    'verifikator' => $this->verifikator,
                ]
            );

        if ($this->permohonan->reklame->bukti_bayar_filepath) {
            $mail_message->attach(storage_path('app/' . $this->permohonan->reklame->bukti_bayar_filepath));
        }

        return $mail_message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
