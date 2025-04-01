<?php
namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\TahunAjaran;
use App\Models\Administrasi;
use Illuminate\Http\Request;
use App\Exports\KeuanganExport;
use App\Exports\PendaftaranExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function indexKeuangan()
{
    $tahunAjarans = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();

    // Get active tahun ajaran
    $activeTahunAjaran = TahunAjaran::where('is_active', true)->first();

    // Calculate summary for active tahun ajaran
    $summary = $this->calculateSummary($activeTahunAjaran->id);

    return view('laporan.keuangan', compact('tahunAjarans', 'summary'));
}



public function indexPendaftaran(Request $request)
{
    // Ambil semua tahun ajaran, diurutkan berdasarkan tahun ajaran terbaru
    $tahunAjarans = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();

    // Ambil ID tahun ajaran yang dipilih, atau default ke tahun ajaran yang aktif
    $tahunAjaranId = $request->tahun_ajaran_id ?? TahunAjaran::where('is_active', true)->first()->id;

    // Hitung data ringkasan
    $summary = [
        'total_pendaftar' => Pendaftaran::where('tahun_ajaran_id', $tahunAjaranId)->count(),
        'total_lulus' => Pendaftaran::where('tahun_ajaran_id', $tahunAjaranId)
            ->where('status_seleksi', 'Lulus')->count(),
        'total_tidak_lulus' => Pendaftaran::where('tahun_ajaran_id', $tahunAjaranId)
            ->where('status_seleksi', 'Tidak Lulus')->count(),
        'total_pending' => Pendaftaran::where('tahun_ajaran_id', $tahunAjaranId)
            ->where('status_seleksi', 'Pending')->count(),
    ];

    // Hitung persentase
    $summary['persentase_lulus'] = ($summary['total_pendaftar'] > 0)
        ? round(($summary['total_lulus'] / $summary['total_pendaftar']) * 100, 1)
        : 0;

    $summary['persentase_tidak_lulus'] = ($summary['total_pendaftar'] > 0)
        ? round(($summary['total_tidak_lulus'] / $summary['total_pendaftar']) * 100, 1)
        : 0;

    // Ambil data jumlah pendaftar per jurusan
    $jurusanData = Pendaftaran::where('tahun_ajaran_id', $tahunAjaranId)
        ->select('jurusan_id', DB::raw('count(*) as total'))
        ->groupBy('jurusan_id')
        ->with('jurusan') // Pastikan relasi jurusan ada di model Pendaftaran
        ->get();

    $summary['labels_jurusan'] = $jurusanData->pluck('jurusan.nama_jurusan')->toArray();
    $summary['data_jurusan'] = $jurusanData->pluck('total')->toArray();

    // Kirim data ke view
    return view('laporan.pendaftaran', compact('tahunAjarans', 'summary'));
}


    private function calculatePendaftaranStatistik($tahunAjaranId)
    {
        $pendaftaran = Pendaftaran::with('jurusan')
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->get();

        return [
            'total_pendaftar' => $pendaftaran->count(),
            'per_jurusan' => $pendaftaran->groupBy('jurusan.nama_jurusan')
                ->map->count(),
            'per_status' => $pendaftaran->groupBy('status_seleksi')
                ->map->count(),
            'jenis_kelamin' => [
                'L' => $pendaftaran->where('jenis_kelamin', 'L')->count(),
                'P' => $pendaftaran->where('jenis_kelamin', 'P')->count()
            ],
            'status_pembayaran' => Administrasi::where('tahun_ajaran_id', $tahunAjaranId)
                ->get()
                ->groupBy('status_pembayaran')
                ->map->count()
        ];
    }

    private function calculateSummary($tahunAjaranId)
    {
        $administrasi = Administrasi::where('tahun_ajaran_id', $tahunAjaranId)
            ->with('pendaftaran')
            ->get();

        $totalPendapatan = $administrasi->sum('total_bayar');
        $totalTarget = $administrasi->sum('total_biaya');

        $pembayaranLunas = $administrasi->where('status_pembayaran', 'Lunas')->count();
        $totalSiswa = $administrasi->count();

        return [
            'total_pendapatan' => $totalPendapatan,
            'persentase_pendapatan' => $totalTarget > 0 ? round(($totalPendapatan / $totalTarget) * 100, 2) : 0,
            'pembayaran_lunas' => $pembayaranLunas,
            'pembayaran_belum_lunas' => $totalSiswa - $pembayaranLunas,
            'persentase_lunas' => $totalSiswa > 0 ? round(($pembayaranLunas / $totalSiswa) * 100, 2) : 0,
            'persentase_belum_lunas' => $totalSiswa > 0 ? round((($totalSiswa - $pembayaranLunas) / $totalSiswa) * 100, 2) : 0,
            'total_pendaftaran' => $administrasi->sum('biaya_pendaftaran'),
            'total_ppdb' => $administrasi->sum('biaya_ppdb'),
            'total_mpls' => $administrasi->sum('biaya_mpls'),
            'total_awal_tahun' => $administrasi->sum('biaya_awal_tahun')
        ];
    }




    public function exportKeuangan(Request $request)
    {
        try {
            $tahunAjaran = TahunAjaran::findOrFail($request->tahun_ajaran_id);

            // Replace the forward slash with underscore for safe filename
            $tahunAjaranStr = str_replace('/', '_', $tahunAjaran->tahun_ajaran);

            return Excel::download(
                new KeuanganExport($tahunAjaran),
                'Laporan_Pendaftaran_' . $tahunAjaranStr . '.xlsx'
            );
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengexport data: ' . $e->getMessage());
        }
    }

    public function exportPendaftaran(Request $request)
    {
        try {
            $tahunAjaran = TahunAjaran::findOrFail($request->tahun_ajaran_id);

            // Replace the forward slash with underscore for safe filename
            $tahunAjaranStr = str_replace('/', '_', $tahunAjaran->tahun_ajaran);

            return Excel::download(
                new PendaftaranExport($tahunAjaran),
                'Laporan_Pendaftaran_' . $tahunAjaranStr . '.xlsx'
            );
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengexport data: ' . $e->getMessage());
        }
    }


}
