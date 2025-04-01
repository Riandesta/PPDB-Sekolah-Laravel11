<?php

namespace App\Models;

use App\Models\Jurusan;
use App\Models\Pendaftaran;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kelas extends Model
{
    use HasFactory;

    protected $fillable = [
        'jurusan_id',
        'tahun_ajaran_id',
        'nama_kelas',
        'urutan_kelas',
        'kapasitas_saat_ini',
        'kapasitas_max'
    ];

    protected $appends = ['is_full'];

    protected $casts = [
        'kapasitas_saat_ini' => 'integer',
        'kapasitas_max' => 'integer',
    ];

   // app/Models/Kelas.php
public function jurusan()
{
    return $this->belongsTo(Jurusan::class);
}

public function pendaftaran()
{
    return $this->hasMany(Pendaftaran::class);
}

public function tahunAjaran()
{
    return $this->belongsTo(TahunAjaran::class);
}


public function isKapasitasAvailable()
{
    return $this->kapasitas_saat_ini < $this->kapasitas_max;
}

    public function getIsFullAttribute(): bool
    {
        return $this->kapasitas_saat_ini >= $this->kapasitas_max;
    }

    public function scopeAvailable($query)
    {
        return $query->where('kapasitas_saat_ini', '<', DB::raw('kapasitas_max'));
    }

    public function scopeForJurusan($query, $jurusanId)
    {
        return $query->where('jurusan_id', $jurusanId);
    }

    public function scopeForTahunAjaran($query, $tahunAjaranId)
    {
        return $query->where('tahun_ajaran_id', $tahunAjaranId);
    }
}
