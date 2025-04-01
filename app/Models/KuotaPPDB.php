<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KuotaPPDB extends Model
{
    protected $table = 'kuota_ppdb';

    protected $fillable = [
        'tahun_ajaran_id',
        'jurusan_id',
        'kuota'
    ];

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    // Helper method untuk cek ketersediaan kuota
    public function isKuotaAvailable()
    {
        $pendaftarCount = Pendaftaran::where('tahun_ajaran_id', $this->tahun_ajaran_id)
            ->where('jurusan_id', $this->jurusan_id)
            ->where('status_seleksi', '!=', 'Pending')
            ->count();

        return $pendaftarCount < $this->kuota;
    }
}
