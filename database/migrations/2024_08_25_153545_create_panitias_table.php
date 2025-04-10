<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('panitias', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('password');
            $table->string('nama');
            $table->string('jabatan');
            $table->string('unit');
            $table->string('alamat');
            $table->string('no_hp')->unique();
            $table->string('email')->unique();
            $table->string('foto')->nullable();
            $table->timestamps();



        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('panitias');
    }
};
