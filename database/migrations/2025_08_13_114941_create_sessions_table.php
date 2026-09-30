<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary(); // ID sesi sebagai primary key
            $table->foreignId('user_id')->nullable()->constrained('users', 'id_users')->onDelete('cascade'); // Relasi ke tabel users
            $table->string('ip_address', 45)->nullable(); // IP address pengguna
            $table->string('user_agent')->nullable(); // Informasi user agent
            $table->text('payload')->nullable(); // Data sesi
            $table->unsignedBigInteger('last_activity')->nullable(); // Aktivitas terakhir
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sessions');
    }
}