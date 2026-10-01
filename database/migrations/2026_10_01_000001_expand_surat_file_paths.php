<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->text('file_surat')->nullable()->change();
            $table->text('file_bukti_terima')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->string('file_surat', 255)->nullable()->change();
            $table->string('file_bukti_terima', 255)->nullable()->change();
        });
    }
};