<?php

namespace App\Jobs;

use App\Models\Permohonan;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use App\Enums\StatusPermohonanEnum;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdatePermohonanRevisiToExpiredJob implements ShouldQueue
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
        $last3Months = now()->subMonths(3);
        $permohonans = Permohonan::where('status', StatusPermohonanEnum::REVISI->value)->where('updated_at', '<=', $last3Months)->get();
        DB::beginTransaction();
        try {
            $permohonans->each(function ($permohonan) use ($last3Months) {
                $permohonan->update([
                    'status' => StatusPermohonanEnum::EXPIRED->value,
                    'is_expired' => true
                ]);
            });
            Log::info('Permohonan revisi expired: ' . $permohonans->count());
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
        }
    }
}
