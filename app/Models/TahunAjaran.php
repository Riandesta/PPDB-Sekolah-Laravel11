<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    protected $table = 'tahun_ajaran';
    protected $guarded = ['id'];

    protected $fillable = [
        'tahun_ajaran',
        'tahun_mulai',
        'tahun_selesai',
        'tanggal_mulai',
        'tanggal_selesai',
        'biaya_pendaftaran',
        'biaya_ppdb',
        'biaya_mpls',
        'biaya_awal_tahun',
        'is_active'
    ];

    protected $casts = [
        'tahun_mulai' => 'integer',
        'tahun_selesai' => 'integer',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_active' => 'boolean',
        'biaya_pendaftaran' => 'decimal:2',
        'biaya_ppdb' => 'decimal:2',
        'biaya_mpls' => 'decimal:2',
        'biaya_awal_tahun' => 'decimal:2'
    ];

    public function pendaftarans()
    {
        return $this->hasMany(Pendaftaran::class);
    }

    public function getTotalBiayaAttribute()
    {
        return $this->biaya_pendaftaran +
               $this->biaya_ppdb +
               $this->biaya_mpls +
               $this->biaya_awal_tahun;
    }
    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }
    public function administrasi()
    {
        return $this->hasMany(Administrasi::class);
    }
    public function getNamaAttribute()
    {
        return $this->attributes['tahun_ajaran'] ?? $this->attributes['nama'];
    }
}
