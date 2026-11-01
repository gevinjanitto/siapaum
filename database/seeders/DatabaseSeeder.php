<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\SektorIzin;
use App\Models\KategoriIzin;
use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\OpenApiUserSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            OpenApiUserSeeder::class,
        ]);

        // generate group layanan skm
        if (config('app.env') != 'production') {
            $this->call([
                GroupLayananSkmSeeder::class,
                DummyDataSeeder::class,
            ]);
            if (KategoriIzin::count() < 5) {
                KategoriIzin::factory()->count(5)->has(
                    SektorIzin::factory()->count(5)
                )->create();
            }
            $this->call(DummyLengkapSeeder::class);
        }
    }
}
