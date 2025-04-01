<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use App\Models\KuotaPPDB;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class KuotaPPDBSeeder extends Seeder
{
    public function run()
    {
       // Di KuotaPPDBSeeder.php
$tahunAjaran = TahunAjaran::where('is_active', true)->first();
if (!$tahunAjaran) {
    $this->command->error('Tidak ada tahun ajaran yang aktif. Jalankan php artisan tahun-ajaran:generate terlebih dahulu');
    return;
}


        // Ambil semua jurusan
        $jurusans = Jurusan::all();

        // Buat data Kuota PPDB untuk setiap jurusan
        foreach ($jurusans as $jurusan) {
            KuotaPPDB::create([
                'tahun_ajaran_id' => $tahunAjaran->id,
                'jurusan_id' => $jurusan->id,
                'kuota' => $jurusan->kapasitas_per_kelas * $jurusan->max_kelas
            ]);
        }

        $this->command->info('Seeder Kuota PPDB berhasil dijalankan untuk tahun ajaran ' . $tahunAjaran->tahun_ajaran);
    }
}
