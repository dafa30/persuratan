<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Hanya seed role, TIDAK membuat user
        $this->call([
            RolesExtraSeeder::class,
        ]);
    }
}
