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
        Schema::create('pendaftarans', function (Blueprint $table) {
            $table->id();
            $table->string('daftar_id')->uniqid();
            $table->string('NISN');
            $table->string('nama');
            $table->string('alamat');
            $table->date('tgl_lahir');
            $table->string('tmp_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('agama');
            $table->string('asal_sekolah');
            $table->string('nama_ortu');
            $table->string('pekerjaan_ortu');
            $table->string('no_telp_ortu');
            $table->string('foto')->nullable();
            $table->boolean('status_dokumen')->default(false);
            $table->decimal('nilai_semester_1', 5, 2)->nullable();
            $table->decimal('nilai_semester_2', 5, 2)->nullable();
            $table->decimal('nilai_semester_3', 5, 2)->nullable();
            $table->decimal('nilai_semester_4', 5, 2)->nullable();
            $table->decimal('nilai_semester_5', 5, 2)->nullable();
            $table->decimal('rata_rata_nilai', 5, 2)->nullable();
            $table->enum('status_seleksi', ['Pending', 'Lulus', 'Tidak Lulus'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftarans');
    }
};
