<?php

namespace App\Console\Commands;

use App\Jobs\SendUsulanNeedVerifikasiNotifcationJob;
use Illuminate\Console\Command;

class SendUsulanNeedVerifikasiNotifcationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:usulan-need-verifikasi-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification to verifikator if there is usulan need to be verified';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        SendUsulanNeedVerifikasiNotifcationJob::dispatch();
    }
}
