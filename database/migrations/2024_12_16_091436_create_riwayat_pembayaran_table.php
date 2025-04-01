<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Migration: create_riwayat_pembayaran_table.php
public function up()
{
    Schema::create('riwayat_pembayaran', function (Blueprint $table) {
        $table->id();
        $table->foreignId('administrasi_id')->constrained('administrasis');
        $table->string('no_pembayaran')->unique()->default('auto');
        $table->datetime('tanggal_bayar');
        $table->enum('jenis_pembayaran', ['pendaftaran', 'ppdb', 'mpls', 'awal_tahun']);
        $table->decimal('jumlah_bayar', 10, 2);
        $table->string('metode_pembayaran');
        $table->string('bukti_pembayaran')->nullable();
        $table->enum('status', ['success', 'pending', 'failed'])->default('pending');
        $table->text('keterangan')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_pembayaran');
    }
};
