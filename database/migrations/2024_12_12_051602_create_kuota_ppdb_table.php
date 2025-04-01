// database/migrations/xxxx_xx_xx_create_kuota_ppdb_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kuota_ppdb', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran');
            $table->foreignId('jurusan_id')->constrained('jurusans');
            $table->integer('kuota')->default(0);
            $table->timestamps();

            // Unique constraint untuk memastikan tidak ada duplikasi
            $table->unique(['tahun_ajaran_id', 'jurusan_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('kuota_ppdb');
    }
};
