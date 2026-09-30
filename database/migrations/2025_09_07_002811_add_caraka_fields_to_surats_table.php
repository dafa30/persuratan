<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            // Cek kolom acuan sekali di awal
            $hasUserId   = Schema::hasColumn('surats', 'user_id');
            $hasPenerima = Schema::hasColumn('surats', 'penerima_id');
            $hasFile     = Schema::hasColumn('surats', 'file_surat');

            // pengirim_nama
            if (!Schema::hasColumn('surats', 'pengirim_nama')) {
                $col = $table->string('pengirim_nama')->nullable();
                if ($hasUserId) { $col->after('user_id'); }
            }

            // penerima_eksternal
            if (!Schema::hasColumn('surats', 'penerima_eksternal')) {
                $col = $table->string('penerima_eksternal')->nullable();
                if ($hasPenerima) { $col->after('penerima_id'); }
            }

            // file_bukti_terima
            if (!Schema::hasColumn('surats', 'file_bukti_terima')) {
                $col = $table->string('file_bukti_terima')->nullable();
                if ($hasFile) { $col->after('file_surat'); }
            }
        });
    }

    public function down(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            if (Schema::hasColumn('surats', 'file_bukti_terima')) {
                $table->dropColumn('file_bukti_terima');
            }
            if (Schema::hasColumn('surats', 'penerima_eksternal')) {
                $table->dropColumn('penerima_eksternal');
            }
            if (Schema::hasColumn('surats', 'pengirim_nama')) {
                $table->dropColumn('pengirim_nama');
            }
        });
    }
};
