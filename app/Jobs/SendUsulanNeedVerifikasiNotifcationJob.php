<?php

namespace App\Jobs;

use App\Models\User;
use App\Enums\RoleEnum;
use App\Models\Permohonan;
use Illuminate\Bus\Queueable;
use App\Enums\StatusPermohonanEnum;
use App\Services\VerifikatorService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\UsulanNeedVerifikasiNotification;

class SendUsulanNeedVerifikasiNotifcationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $verifikatorService = new VerifikatorService();
        $verifikator = User::where('role_id', RoleEnum::VERIFIKATOR->value)->get();

        $permohonan = Permohonan::whereIn('status', [
            StatusPermohonanEnum::VERIFIKASI->value,
            StatusPermohonanEnum::VERIFIKASI_ULANG->value
        ])->whereHas('alurPermohonan', function ($query) {
            $query->where('is_done', false);
        })->get();

        foreach($verifikator as $v) {
            $count = 0;
            foreach($permohonan as $p) {
                if ($verifikatorService->isVerifikatorTurn($p, $v)) {
                    $count++;
                    $permohonan = $permohonan->filter(function ($permohonan) use ($p) {
                        return $permohonan->id !== $p->id;
                    });
                }
            }

            if ($count > 0) {
                $v->notify(new UsulanNeedVerifikasiNotification($count));
            }
        }
    }
}
