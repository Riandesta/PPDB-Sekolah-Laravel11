<?php
namespace Database\Seeders;

use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    public function run()
    {
        $tahunAjarans = [
            [
                'tahun_ajaran' => '2024/2025',
                'tahun_mulai' => 2024,
                'tahun_selesai' => 2025,
                'tanggal_mulai' => '2024-07-01',
                'tanggal_selesai' => '2024-12-20',
                'biaya_pendaftaran' => 500000,  // Add this line
                'biaya_ppdb' => 300000,         // Add this line
                'biaya_mpls' => 200000,         // Add this line
                'biaya_awal_tahun' => 1000000,  // Add this line
                'is_active' => true
            ],
            [
                'tahun_ajaran' => '2025/2026',
                'tahun_mulai' => 2025,
                'tahun_selesai' => 2026,
                'tanggal_mulai' => '2024-07-01',
                'tanggal_selesai' => '2024-12-20',
                'biaya_pendaftaran' => 550000,  // Add this line
                'biaya_ppdb' => 320000,         // Add this line
                'biaya_mpls' => 250000,         // Add this line
                'biaya_awal_tahun' => 1100000,  // Add this line
                'is_active' => false
            ],
            [
                'tahun_ajaran' => '2026/2027',
                'tahun_mulai' => 2026,
                'tahun_selesai' => 2027,
                'tanggal_mulai' => '2024-07-01',
                'tanggal_selesai' => '2024-12-20',
                'biaya_pendaftaran' => 600000,  // Add this line
                'biaya_ppdb' => 350000,         // Add this line
                'biaya_mpls' => 300000,         // Add this line
                'biaya_awal_tahun' => 1200000,  // Add this line
                'is_active' => false
            ]
        ];

        foreach ($tahunAjarans as $ta) {
            TahunAjaran::create($ta);
        }
    }
}
