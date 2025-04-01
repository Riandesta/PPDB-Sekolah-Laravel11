<?php
namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run()
    {
        $tahunAjaran = TahunAjaran::where('is_active', true)->first();
        $jurusans = Jurusan::all();

        foreach ($jurusans as $jurusan) {
            for ($i = 1; $i <= $jurusan->max_kelas; $i++) {
                Kelas::create([
                    'jurusan_id' => $jurusan->id,
                    'nama_kelas' => $jurusan->kode_jurusan . ' ' . $i,
                    'tahun_ajaran' => $tahunAjaran->tahun_ajaran,
                    'tahun_ajaran_id' => $tahunAjaran->id,
                    'urutan_kelas' => $i,
                    'kapasitas_saat_ini' => 0
                ]);
            }
        }
    }
}
    