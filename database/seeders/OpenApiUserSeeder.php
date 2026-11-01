<?php

namespace Database\Seeders;

use App\Models\OpenApiUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OpenApiUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OpenApiUser::firstOrCreate([
            'name' => 'Open API User',
            'username' => 'open-api-user',
            'email' => '-',
        ], [
            'password' => bcrypt('password'),
        ]);
    }
}
