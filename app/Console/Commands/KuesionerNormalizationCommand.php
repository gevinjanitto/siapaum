<?php

namespace App\Console\Commands;

use App\Models\KuesionerJawaban;
use Illuminate\Console\Command;

class KuesionerNormalizationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kuesioner:normalization';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Normalize kuesioner data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $kuesioner_jawabans = KuesionerJawaban::with(['kuesionerOpsi.kuesionerPertanyaan'])->whereNull('pertanyaan')->get();

        foreach ($kuesioner_jawabans as $kuesioner_jawaban) {
            $kuesioner_jawaban->update([
                'pertanyaan' => $kuesioner_jawaban->kuesionerOpsi->kuesionerPertanyaan->pertanyaan,
                'opsi' => $kuesioner_jawaban->kuesionerOpsi->opsi,
                'point' => $kuesioner_jawaban->kuesionerOpsi->point,
            ]);
        }
    }
}
