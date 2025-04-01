<?php
// database/seeders/PembayaranPPDBSeeder.php

namespace Database\Seeders;

use App\Models\Pendaftaran;
use App\Models\TahunAjaran;
use App\Models\Administrasi;
use Illuminate\Database\Seeder;

class AdministrasiSeeder extends Seeder
{
    public function run()
    {
        // Dapatkan tahun ajaran yang aktif
        $tahunAjaran = TahunAjaran::where('is_active', true)->first();

        if (!$tahunAjaran) {
            $this->command->error('Tahun Ajaran aktif tidak ditemukan!');
            return;
        }

        // Gunakan tahun ajaran yang ada untuk membuat data administrasi
        $biayaPendaftaran = config('ppdb.biaya_pendaftaran', 100000);
        $biayaPPDB = config('ppdb.biaya_ppdb', 5000000);
        $biayaMPLS = config('ppdb.biaya_mpls', 250000);
        $biayaAwalTahun = config('ppdb.biaya_awal_tahun', 1500000);

        $administrasis = [];

        // Buat data administrasi untuk setiap pendaftaran yang belum memiliki data administrasi
        $pendaftaranTanpaAdministrasi = Pendaftaran::whereDoesntHave('administrasi')
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->get();

        foreach ($pendaftaranTanpaAdministrasi as $pendaftaran) {
            $administrasis[] = [
                'pendaftaran_id' => $pendaftaran->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'biaya_pendaftaran' => $biayaPendaftaran,
                'biaya_ppdb' => $biayaPPDB,
                'biaya_mpls' => $biayaMPLS,
                'biaya_awal_tahun' => $biayaAwalTahun,
                'total_bayar' => 0,
                'status_pembayaran' => 'Belum Lunas',
                'is_pendaftaran_lunas' => false,
                'is_ppdb_lunas' => false,
                'is_mpls_lunas' => false,
                'is_awal_tahun_lunas' => false,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        if (!empty($administrasis)) {
            Administrasi::insert($administrasis);
            $this->command->info('Data administrasi berhasil dibuat untuk ' . count($administrasis) . ' pendaftar.');
        } else {
            $this->command->info('Semua pendaftar sudah memiliki data administrasi.');
        }
    }
}
