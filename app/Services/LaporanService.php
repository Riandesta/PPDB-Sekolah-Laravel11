<?php
namespace App\Services;

use App\Models\Pendaftaran;
use App\Models\Administrasi;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;

class LaporanService
{
    public function getLaporanKeuangan($tahunAjaranId = null, $periode = null)
    {
        $query = Administrasi::with(['pendaftaran.jurusan', 'tahunAjaran'])
            ->select(
                'administrasi.*',
                DB::raw('SUM(total_bayar) as total_pembayaran'),
                DB::raw('COUNT(CASE WHEN status_pembayaran = "Lunas" THEN 1 END) as jumlah_lunas'),
                DB::raw('COUNT(CASE WHEN status_pembayaran = "Belum Lunas" THEN 1 END) as jumlah_belum_lunas')
            );

        if ($tahunAjaranId) {
            $query->where('tahun_ajaran_id', $tahunAjaranId);
        }

        if ($periode) {
            $query->whereBetween('created_at', $periode);
        }

        $summary = $query->first();

        $detailPerJurusan = Administrasi::with(['pendaftaran.jurusan'])
            ->select(
                'jurusan_id',
                DB::raw('COUNT(*) as total_siswa'),
                DB::raw('SUM(total_bayar) as total_pembayaran'),
                DB::raw('SUM(CASE WHEN status_pembayaran = "Lunas" THEN 1 ELSE 0 END) as jumlah_lunas')
            )
            ->join('pendaftaran', 'administrasi.pendaftaran_id', '=', 'pendaftaran.id')
            ->groupBy('jurusan_id')
            ->get();

        return [
            'summary' => $summary,
            'detail_per_jurusan' => $detailPerJurusan,
            'riwayat_pembayaran' => $this->getRiwayatPembayaran($tahunAjaranId)
        ];
    }

    public function getRiwayatPembayaran($tahunAjaranId = null)
    {
        $query = Administrasi::with(['pendaftaran'])
            ->select(
                DB::raw('DATE(created_at) as tanggal'),
                DB::raw('SUM(total_bayar) as total_harian'),
                DB::raw('COUNT(*) as jumlah_transaksi')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('tanggal', 'desc');

        if ($tahunAjaranId) {
            $query->where('tahun_ajaran_id', $tahunAjaranId);
        }

        return $query->get();
    }

    public function getLaporanPendaftaran($tahunAjaranId = null)
    {
        $query = Pendaftaran::with(['jurusan', 'administrasi'])
            ->select(
                DB::raw('COUNT(*) as total_pendaftar'),
                DB::raw('COUNT(CASE WHEN status_seleksi = "Lulus" THEN 1 END) as total_lulus'),
                DB::raw('COUNT(CASE WHEN status_seleksi = "Tidak Lulus" THEN 1 END) as total_tidak_lulus'),
                DB::raw('COUNT(CASE WHEN status_seleksi = "Pending" THEN 1 END) as total_pending'),
                DB::raw('AVG(rata_rata_nilai) as rata_rata_nilai')
            );

        if ($tahunAjaranId) {
            $query->where('tahun_ajaran_id', $tahunAjaranId);
        }

        $summary = $query->first();

        $detailPerJurusan = Pendaftaran::with(['jurusan'])
            ->select(
                'jurusan_id',
                DB::raw('COUNT(*) as total_pendaftar'),
                DB::raw('COUNT(CASE WHEN status_seleksi = "Lulus" THEN 1 END) as total_lulus'),
                DB::raw('AVG(rata_rata_nilai) as rata_rata_nilai')
            )
            ->groupBy('jurusan_id')
            ->get();

        return [
            'summary' => $summary,
            'detail_per_jurusan' => $detailPerJurusan,
            'statistik_nilai' => $this->getStatistikNilai($tahunAjaranId)
        ];
    }

    private function getStatistikNilai($tahunAjaranId = null)
    {
        $query = Pendaftaran::select(
            DB::raw('FLOOR(rata_rata_nilai/10)*10 as range_nilai'),
            DB::raw('COUNT(*) as jumlah_siswa')
        )
        ->groupBy('range_nilai')
        ->orderBy('range_nilai');

        if ($tahunAjaranId) {
            $query->where('tahun_ajaran_id', $tahunAjaranId);
        }

        return $query->get();
    }
}
