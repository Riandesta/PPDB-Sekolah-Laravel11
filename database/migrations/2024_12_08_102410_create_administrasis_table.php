<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration

{
    public function up()
    {
        Schema::create('administrasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftarans')->onDelete('cascade');
            $table->string('no_bayar')->unique();
            $table->datetime('tanggal_bayar')->nullable();
            // Biaya diambil dari tahun_ajaran sebagai snapshot
            $table->decimal('biaya_pendaftaran', 10, 2);
            $table->decimal('biaya_ppdb', 10, 2);
            $table->decimal('biaya_awal_tahun', 10, 2);
            $table->decimal('biaya_mpls', 10, 2);
            $table->decimal('total_bayar', 10, 2)->default(0);
            $table->enum('status_pembayaran', ['Lunas', 'Belum Lunas'])->default('Belum Lunas');
            // Tracking status pembayaran per komponen
            $table->boolean('is_pendaftaran_lunas')->default(false);
            $table->boolean('is_ppdb_lunas')->default(false);
            $table->boolean('is_mpls_lunas')->default(false);
            $table->boolean('is_awal_tahun_lunas')->default(false);
            // Tanggal pembayaran per komponen
            $table->datetime('tanggal_bayar_pendaftaran')->nullable();
            $table->datetime('tanggal_bayar_ppdb')->nullable();
            $table->datetime('tanggal_bayar_mpls')->nullable();
            $table->datetime('tanggal_bayar_awal_tahun')->nullable();
            // Informasi pembayaran tambahan
            $table->string('metode_pembayaran')->nullable();
            $table->string('bukti_pembayaran')->nullable();
            $table->text('keterangan')->nullable();
            $table->decimal('sisa_pembayaran', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('administrasis');
    }
};
