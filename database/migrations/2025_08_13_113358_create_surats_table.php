<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuratsTable extends Migration
{
    public function up()
    {
        Schema::create('surats', function (Blueprint $table) {
            $table->id('id_surats'); // Mengubah id menjadi id_surats
            $table->string('judul_surat');
            $table->string('nomor_surat');
            $table->string('jenis_surat');
            $table->string('status')->default('dibuat');
            $table->timestamp('dibaca')->nullable();
            $table->string('file_surat')->nullable();
            $table->string('perihal');
            $table->timestamps();
            $table->foreignId('role_id')->nullable()->constrained('roles', 'id_roles')->onDelete('cascade'); // Relasi ke tabel roles
            $table->foreignId('pengirim_id')->nullable()->constrained('users', 'id_users')->onDelete('cascade'); // Relasi ke tabel users
            $table->foreignId('penerima_id')->nullable()->constrained('users', 'id_users')->onDelete('cascade'); // Relasi ke tabel users
        });
    }

    public function down()
    {
        Schema::dropIfExists('surats');
    }
}