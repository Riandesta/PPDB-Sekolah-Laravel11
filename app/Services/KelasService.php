<?php

namespace App\Services;

use App\Models\Kelas;
use App\Models\Jurusan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class KelasService
{
    public function getKelasGroupedByJurusan()
    {
        return Jurusan::with(['kelas' => function($query) {
            $query->withCount('pendaftaran as total_siswa');
        }])->get()->mapWithKeys(function($jurusan) {
            return [$jurusan->nama_jurusan => $jurusan->kelas];
        });
    }

    public function getKelasDetail(Kelas $kelas)
    {
        $kelas->load(['jurusan', 'pendaftaran.administrasi']);
        $pendaftaran = $kelas->pendaftaran;
    
        return [
            'statistik' => [
                'total_siswa' => $pendaftaran->count(),
                'siswa_laki' => $pendaftaran->where('jenis_kelamin', 'L')->count(),
                'siswa_perempuan'    => $pendaftaran->where('jenis_kelamin', 'P')->count(),
                'siswa_lunas' => $pendaftaran->filter(function($s) {
                    return $s->administrasi && $s->administrasi->status_pembayaran === 'Lunas';
                })->count(),
                'rata_rata_nilai' => $pendaftaran->avg('rata_rata_nilai') ? round($pendaftaran->avg('rata_rata_nilai'), 2) : 0,
            ],
            'siswa' => $pendaftaran->sortBy('nama')->values()
        ];
    }
    

    private function calculateSiswaStatistics($kelas)
    {
        return [
            'total' => $kelas->pendaftaran()->count(),
            'kapasitas' => $kelas->jurusan->kapasitas_per_kelas,
            'persentase_terisi' => $kelas->pendaftaran()->count() / $kelas->jurusan->kapasitas_per_kelas * 100,
            'distribusi_jk' => $kelas->pendaftaran()
                ->select('jenis_kelamin', DB::raw('count(*) as total'))
                ->groupBy('jenis_kelamin')
                ->pluck('total', 'jenis_kelamin')
        ];
    }

    public function assignSiswaToPendaftaran($pendaftaran)
{
    try {
        return DB::transaction(function () use ($pendaftaran) {
            $firstLetter = strtoupper(substr($pendaftaran->nama, 0, 1));

            // Dapatkan semua kelas untuk jurusan ini
            $kelasCollection = Kelas::where('jurusan_id', $pendaftaran->jurusan_id)
                ->where('tahun_ajaran_id', $pendaftaran->tahun_ajaran_id)
                ->get();

            if ($kelasCollection->isEmpty()) {
                throw new \Exception('Tidak ada kelas tersedia untuk jurusan ini');
            }

            // Hitung distribusi huruf di setiap kelas
            $letterDistribution = [];
            foreach ($kelasCollection as $kelas) {
                $letterDistribution[$kelas->id] = $kelas->pendaftaran()
                    ->whereRaw('UPPER(LEFT(nama, 1)) = ?', [$firstLetter])
                    ->count();
            }

            // Pilih kelas dengan distribusi huruf paling sedikit
            $targetKelasId = array_search(min($letterDistribution), $letterDistribution);

            // Update pendaftaran dengan kelas yang dipilih
            $pendaftaran->update(['kelas_id' => $targetKelasId]);

            // Update kapasitas kelas
            Kelas::find($targetKelasId)->increment('kapasitas_saat_ini');

            // Clear cache
            Cache::forget("kelas.{$targetKelasId}.detail");

            return true;
        });
    } catch (\Exception $e) {
        Log::error('Error assigning siswa to kelas: ' . $e->getMessage());
        throw $e;
    }
}

public function removeSiswaFromKelas($pendaftaran)
{
    return DB::transaction(function () use ($pendaftaran) {
        if ($pendaftaran->kelas_id) {
            $kelasId = $pendaftaran->kelas_id;
            $pendaftaran->update(['kelas_id' => null]);

            // Kurangi kapasitas kelas
            Kelas::find($kelasId)->decrement('kapasitas_saat_ini');

            // Clear cache
            Cache::forget("kelas.{$kelasId}.detail");

            return true;
        }
        return false;
    });
}


    private function getSiswaBerdasarkanHuruf($kelas)
    {
        return $kelas->pendaftaran()
            ->select('*', DB::raw('LEFT(nama, 1) as huruf_awal'))
            ->orderBy('nama')
            ->get()
            ->groupBy('huruf_awal');
    }

    public function getAbsensiData($kelas)
    {
        return [
            'kelas' => $kelas->load('jurusan', 'tahunAjaran'),
            'siswa' => $kelas->pendaftaran()
                ->orderBy('nama')
                ->get()
        ];
    }
}
