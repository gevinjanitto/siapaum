<?php

namespace Database\Seeders;

use App\Models\GroupLayananSkm;
use Illuminate\Database\Seeder;
use PHPUnit\Framework\Attributes\Group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class GroupLayananSkmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // using faker
        $faker = \Faker\Factory::create();

        // generate group layanan skm
        if(GroupLayananSkm::count() > 5) {
            return;
        }
        for ($i = 0; $i < 5; $i++) {
            GroupLayananSkm::create([
                'nama' => $faker->sentence(2),
            ])->layananSkm()->createMany([
                [
                    'nama' => $faker->sentence(3),
                ],
                [
                    'nama' => $faker->sentence(3),
                ],
                [
                    'nama' => $faker->sentence(3),
                ],
            ]);
        }
    }
}
