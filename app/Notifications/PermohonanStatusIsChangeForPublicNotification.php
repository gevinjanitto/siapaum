<?php

namespace App\Notifications;

use App\Enums\StatusPermohonanEnum;
use App\Models\Permohonan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PermohonanStatusIsChangeForPublicNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The Permohonan instance.
     */
    protected Permohonan $permohonan;
    protected string $status;

    protected string $route_url;

    /**
     * Create a new notification instance.
     */
    public function __construct(Permohonan $permohonan)
    {
        $this->permohonan = $permohonan;
        $this->status = strtoupper(StatusPermohonanEnum::tryFrom($permohonan->status)->deskripsi());

        // route url
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
        if ($this->status == "REVISI") {
            $subject = 'Revisi pada Usulan ' . $this->permohonan->nomor_registrasi;
        } else if ($this->status == "SELESAI") {
            $subject = 'Usulan ' . $this->permohonan->nomor_registrasi . ' Telah Selesai';
        }

        return (new MailMessage)
            ->subject($subject)
            ->markdown('emails.permohonan-status-is-change-for-public', [
                'permohonan' => $this->permohonan,
                'status' => $this->status,
                'route_url' => $this->route_url,
            ]);
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
