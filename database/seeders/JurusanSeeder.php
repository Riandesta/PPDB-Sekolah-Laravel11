<?php
namespace Database\Seeders;

use App\Models\Jurusan;
use Illuminate\Database\Seeder;

class JurusanSeeder extends Seeder
{
    public function run()
    {
        $jurusans = [
            [
                'nama_jurusan' => 'Rekayasa Perangkat Lunak',
                'kode_jurusan' => 'RPL',
                'deskripsi' => 'Jurusan yang mempelajari pengembangan perangkat lunak',
                'kapasitas_per_kelas' => 36,
                'max_kelas' => 3
            ],
            [
                'nama_jurusan' => 'Teknik Komputer dan Jaringan',
                'kode_jurusan' => 'TKJ',
                'deskripsi' => 'Jurusan yang mempelajari jaringan komputer dan hardware',
                'kapasitas_per_kelas' => 36,
                'max_kelas' => 3
            ],
            [
                'nama_jurusan' => 'Teknik Permesinan',
                'kode_jurusan' => 'TP',
                'deskripsi' => 'Jurusan yang mempelajari Mesin Bubut',
                'kapasitas_per_kelas' => 36,
                'max_kelas' => 3
            ],
            [
                'nama_jurusan' => 'Teknik Kendaraan Ringan Otomotif',
                'kode_jurusan' => 'TKRO',
                'deskripsi' => 'Jurusan yang mempelajari Otomotif',
                'kapasitas_per_kelas' => 36,
                'max_kelas' => 3
            ],
            [
                'nama_jurusan' => 'Teknik Bisnis Sepeda Motor',
                'kode_jurusan' => 'TBSM',
                'deskripsi' => 'Jurusan yang mempelajari Otomotif',
                'kapasitas_per_kelas' => 36,
                'max_kelas' => 3
            ],
            // Add more jurusan as needed
        ];

        foreach ($jurusans as $jurusan) {
            Jurusan::create($jurusan);
        }
    }
}
