<?php
// database/seeders/RolesExtraSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesExtraSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['nama_role' => 'admin'],
            ['nama_role' => 'sekretariat'],
            ['nama_role' => 'caraka'],
            ['nama_role' => 'user'],
            ['nama_role' => 'bagian_tu'],
            ['nama_role' => 'kearsipan'],
        ];

        foreach ($rows as $r) {
            $exists = DB::table('roles')->where('nama_role', $r['nama_role'])->exists();
            if (!$exists) DB::table('roles')->insert($r);
        }
    }
}
