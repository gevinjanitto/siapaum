<?php

namespace App\Console\Commands;

use App\Jobs\UpdatePermohonanRevisiToExpiredJob;
use Illuminate\Console\Command;

class UpdatePermohonanRevisiToExpiredCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permohonan:revisi-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update permohonan revisi to expired';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        UpdatePermohonanRevisiToExpiredJob::dispatch();
    }
}
