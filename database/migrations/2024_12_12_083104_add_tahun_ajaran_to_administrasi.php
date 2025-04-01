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
        Schema::table('administrasis', function (Blueprint $table) {
            $table->foreignId('tahun_ajaran_id')
            ->nullable()
            ->constrained('tahun_ajaran')
            ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('administrasi', function (Blueprint $table) {
            Schema::dropIfExists('tahun_ajaran_id');
        });
    }
};
