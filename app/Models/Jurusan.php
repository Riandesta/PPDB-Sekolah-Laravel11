<?php

namespace App\Models;

use App\Models\Kelas;
use App\Models\Pendaftaran;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    protected $fillable = [
        'nama_jurusan',
        'kode_jurusan',
        'deskripsi',
        'kapasitas_per_kelas',
        'max_kelas',
    ];

    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }
    public function pendaftaran()
    {
        return $this->hasMany(Pendaftaran::class);
    }
    
}
