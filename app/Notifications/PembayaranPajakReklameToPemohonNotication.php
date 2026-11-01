<?php

namespace App\Notifications;

use App\Models\Permohonan;
use App\Models\Reklame;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PembayaranPajakReklameToPemohonNotication extends Notification implements ShouldQueue
{
    use Queueable;

    protected Permohonan $permohonan;
    protected Reklame $reklame;
    protected $route_url;

    /**
     * Create a new notification instance.
     */
    public function __construct(Permohonan $permohonan)
    {
        $this->permohonan = $permohonan;
        $this->reklame = $permohonan->reklame;
        $this->route_url = route('public.permohonan.show', $permohonan->id);
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
            ->subject('Tagihan Pembayaran Permohonan Izin Reklame')
            ->markdown(
                'emails.pembayaran-pajak-reklame-to-pemohon',
                [
                    'permohonan' => $this->permohonan,
                    'reklame' => $this->reklame,
                    'route_url' => $this->route_url,
                ]
            );

        if ($this->reklame->skpd_filepath) {
            $mail_message->attach(storage_path('app/' . $this->reklame->skpd_filepath));
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
