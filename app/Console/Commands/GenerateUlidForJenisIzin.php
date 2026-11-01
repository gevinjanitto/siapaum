<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JenisIzin;
use Illuminate\Support\Str;

class GenerateUlidForJenisIzin extends Command
{
    protected $signature = 'generate:ulid-jenis-izin';
    protected $description = 'Generate ULID for existing Jenis Izin records';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $jenisIzins = JenisIzin::whereNull('ulid')->get();

        foreach ($jenisIzins as $jenisIzin) {
            $jenisIzin->ulid = (string) Str::ulid();
            $jenisIzin->save();
        }

        $this->info('ULIDs generated for existing Jenis Izin records.');
    }
}
