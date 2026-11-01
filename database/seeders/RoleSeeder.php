<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['id' => 1,'nama' => 'Admin'],
            ['id' => 2,'nama' => 'Verifikator'],
            ['id' => 3,'nama' => 'Public'],
            ['id' => 4, 'nama' => 'Auditor'],
            ['id' => 5, 'nama' => 'Inputer'],
        ];

        foreach($roles as $role){
            Role::firstOrCreate($role);
        }
    }
}
