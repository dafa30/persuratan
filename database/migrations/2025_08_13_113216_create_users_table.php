<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_users'); // Mengubah id menjadi id_users
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->foreignId('role_id')->default(2)->constrained('roles', 'id_roles')->onDelete('cascade'); // Relasi ke tabel roles
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}