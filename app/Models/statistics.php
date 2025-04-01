<?php

namespace App\Models;

use App\Models\KuotaPPDB;
use App\Models\Pendaftaran;
use App\Models\Administrasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Statistics extends Model
{
    public static function getTotalPendaftar()
    {
        return Pendaftaran::count();
    }

    public static function getTotalDiterima()
    {
        return Pendaftaran::where('status_seleksi', 'Lulus')->count();
    }

    public static function getKuotaTersisa()
    {
        $totalKuota = KuotaPPDB::sum('kuota');
        $totalPendaftar = self::getTotalPendaftar();
        return max(0, $totalKuota - $totalPendaftar);
    }

    public static function getPembayaranStats()
    {
        $totalPendaftar = self::getTotalPendaftar();
        $pembayaranLunas = Administrasi::where('status_pembayaran', 'Lunas')->count();
        $pembayaranBelumLunas = $totalPendaftar - $pembayaranLunas;

        return [
            'lunas' => $pembayaranLunas,
            'belum_lunas' => $pembayaranBelumLunas,
            'persentase_lunas' => $totalPendaftar > 0
                ? round(($pembayaranLunas / $totalPendaftar) * 100, 1)
                : 0,
            'total_pembayaran' => Administrasi::where('status_pembayaran', 'Lunas')
                ->sum('total_bayar')
        ];
    }

    public static function getStatistikPerJurusan()
    {
        return Pendaftaran::select('jurusan_id', DB::raw('count(*) as total'))
            ->groupBy('jurusan_id')
            ->with('jurusan')
            ->get()
            ->map(function($item) {
                return [
                    'jurusan' => $item->jurusan->nama_jurusan,
                    'total' => $item->total,
                    'kapasitas' => $item->jurusan->kapasitas_per_kelas * $item->jurusan->max_kelas,
                    'persentase' => round(($item->total / ($item->jurusan->kapasitas_per_kelas * $item->jurusan->max_kelas)) * 100, 1)
                ];
            });
    }
}
