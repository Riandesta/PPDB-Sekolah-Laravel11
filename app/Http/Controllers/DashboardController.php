<?php

namespace App\Http\Controllers;

use App\Models\KuotaPPDB;
use App\Models\Pendaftaran;
use App\Models\Administrasi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Statistik pembayaran
        $pembayaranStats = Administrasi::select(
            'status_pembayaran',
            DB::raw('count(*) as total')
        )->groupBy('status_pembayaran')->get();

        $totalPendaftar = Pendaftaran::count();
        $pembayaranLunas = $pembayaranStats->where('status_pembayaran', 'Lunas')->first()->total ?? 0;
        $pembayaranBelumLunas = $pembayaranStats->where('status_pembayaran', 'Belum Lunas')->first()->total ?? 0;

        // Kuota
        $totalKuota = KuotaPPDB::sum('kuota');
        $sisaKuota = max(0, $totalKuota - $totalPendaftar);

        $sisaKuotaPerJurusan = KuotaPPDB::with('jurusan')
        ->select(
            'jurusan_id',
            'kuota',
            DB::raw('(
                SELECT COUNT(*)
                FROM pendaftarans
                WHERE pendaftarans.jurusan_id = kuota_ppdb.jurusan_id
            ) as terisi'),
            DB::raw('kuota - (
                SELECT COUNT(*)
                FROM pendaftarans
                WHERE pendaftarans.jurusan_id = kuota_ppdb.jurusan_id
            ) as sisa')
        )->get();

        $statistics = [
            'total_pendaftar' => $totalPendaftar,
            'total_diterima' => Pendaftaran::where('status_seleksi', 'Lulus')->count(),
            'total_pembayaran' => Administrasi::where('status_pembayaran', 'Lunas')->sum('total_bayar'),
            'sisa_kuota' => $sisaKuota,
            'sisa_kuota_per_jurusan' => $sisaKuotaPerJurusan,
            'pendaftar_per_jurusan' => Pendaftaran::select('jurusan_id', DB::raw('count(*) as total'))
                ->groupBy('jurusan_id')
                ->with('jurusan')
                ->get(),
            'pembayaran_lunas' => $pembayaranLunas,
            'pembayaran_belum_lunas' => $pembayaranBelumLunas,
            'persentase_lunas' => $totalPendaftar > 0 ? round(($pembayaranLunas / $totalPendaftar) * 100, 1) . '%' : '0%',
            'persentase_belum_lunas' => $totalPendaftar > 0 ? round(($pembayaranBelumLunas / $totalPendaftar) * 100, 1) . '%' : '0%',
            'pendaftar_per_hari' => Pendaftaran::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total')
            )->groupBy('date')->get(),
            'status_seleksi' => Pendaftaran::select(
                'status_seleksi',
                DB::raw('count(*) as total')
            )->groupBy('status_seleksi')->get(),
            'persentase_kelulusan' => round(
                Pendaftaran::where('status_seleksi', 'Lulus')->count() / max(1, Pendaftaran::count()) * 100,
                1
            ) . '%'
        ];

        $pendaftaranStats = Pendaftaran::select(
            DB::raw('DATE(created_at) as tanggal'),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        // Format data untuk chart
        $labels = [];
        $data = [];

        // Isi data untuk 30 hari terakhir
        for ($i = 30; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $total = $pendaftaranStats->firstWhere('tanggal', $date)?->total ?? 0;

            $labels[] = Carbon::parse($date)->format('d M');
            $data[] = $total;
        }

        // Tambahkan ke array statistics
        $statistics['labels'] = $labels;
        $statistics['data'] = $data;

        // Tambahkan data kumulatif
        $statistics['data_kumulatif'] = array_reduce($data, function ($carry, $item) {
            $last = end($carry) ?? 0;
            $carry[] = $last + $item;
            return $carry;
        }, []);


        $title = 'Dashboard';
        return view('dashboard.index', compact('statistics', 'title'));
    }
}
