<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            // letakkan setelah 'jenis_surat' kalau ada, aman kalau tidak ada
            if (!Schema::hasColumn('surats', 'kategori')) {
                $col = $table->string('kategori', 20)->default('biasa');
                if (Schema::hasColumn('surats', 'jenis_surat')) {
                    $col->after('jenis_surat');
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            if (Schema::hasColumn('surats', 'kategori')) {
                $table->dropColumn('kategori');
            }
        });
    }
};
