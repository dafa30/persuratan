<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->index('kategori', 'surats_kategori_index');
            $table->index('status', 'surats_status_index');
            $table->index('created_at', 'surats_created_at_index');
        });

        // SQLite (dipakai test) tidak mendukung penggantian foreign key lewat ALTER.
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        // Surat arsip tidak boleh ikut terhapus diam-diam saat user atau role dihapus.
        Schema::table('surats', function (Blueprint $table) {
            $table->dropForeign(['pengirim_id']);
            $table->dropForeign(['penerima_id']);
            $table->dropForeign(['role_id']);
        });
        Schema::table('surats', function (Blueprint $table) {
            $table->foreign('pengirim_id')->references('id_users')->on('users')->restrictOnDelete();
            $table->foreign('penerima_id')->references('id_users')->on('users')->restrictOnDelete();
            $table->foreign('role_id')->references('id_roles')->on('roles')->restrictOnDelete();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('role_id')->references('id_roles')->on('roles')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['role_id']);
            });
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('role_id')->references('id_roles')->on('roles')->cascadeOnDelete();
            });

            Schema::table('surats', function (Blueprint $table) {
                $table->dropForeign(['pengirim_id']);
                $table->dropForeign(['penerima_id']);
                $table->dropForeign(['role_id']);
            });
            Schema::table('surats', function (Blueprint $table) {
                $table->foreign('pengirim_id')->references('id_users')->on('users')->cascadeOnDelete();
                $table->foreign('penerima_id')->references('id_users')->on('users')->cascadeOnDelete();
                $table->foreign('role_id')->references('id_roles')->on('roles')->cascadeOnDelete();
            });
        }

        Schema::table('surats', function (Blueprint $table) {
            $table->dropIndex('surats_kategori_index');
            $table->dropIndex('surats_status_index');
            $table->dropIndex('surats_created_at_index');
        });
    }
};
