<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // 2024_12_10_072518_create_tahun_ajaran_table.php
public function up()
{
    Schema::create('tahun_ajaran', function (Blueprint $table) {
        $table->id();
        $table->string('tahun_ajaran');
        $table->year('tahun_mulai');
        $table->year('tahun_selesai');
        $table->boolean('is_active')->default(false);
        $table->date('tanggal_mulai');
        $table->date('tanggal_selesai');
        // Tambahkan kolom biaya
        $table->decimal('biaya_pendaftaran', 10, 2);
        $table->decimal('biaya_ppdb', 10, 2);
        $table->decimal('biaya_awal_tahun', 10, 2);
        $table->decimal('biaya_mpls', 10, 2);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tahun_ajaran');
    }
};
