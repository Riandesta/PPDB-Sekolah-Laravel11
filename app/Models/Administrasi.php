<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Administrasi extends Model
{

    protected $table = 'administrasis';
    protected $fillable = [
        'pendaftaran_id',
        'no_bayar',
        'tahun_ajaran_id',
        'biaya_pendaftaran',
        'biaya_ppdb',
        'biaya_mpls',
        'biaya_awal_tahun',
        'total_biaya',
        'total_bayar',
        'sisa_pembayaran',
        'status_pembayaran',
        'keterangan',
        'sisa_pembayaran',
    ];



    protected $casts = [
        'biaya_pendaftaran' => 'integer',
        'biaya_ppdb' => 'integer',
        'biaya_mpls' => 'integer',
        'biaya_awal_tahun' => 'integer',
        'total_biaya' => 'integer',
        'total_bayar' => 'integer',
        'sisa_pembayaran' => 'integer',
       'tanggal_bayar_pendaftaran' => 'datetime',
       'tanggal_bayar_ppdb'  => 'datetime',
       'tanggal_bayar_mpls'  => 'datetime',
       'tanggal_bayar_awal_tahun'  => 'datetime',
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class, 'pendaftaran_id');
    }


    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function riwayatPembayaran(): HasMany
{
    return $this->hasMany(RiwayatPembayaran::class)->orderBy('created_at', 'desc');
}

    public function updateStatusPembayaran(): void
    {
        $this->sisa_pembayaran = $this->total_biaya - $this->total_bayar;
        $this->status_pembayaran = $this->sisa_pembayaran <= 0 ? 'Lunas' : 'Belum Lunas';
        $this->save();
    }

    public function totalBayarUntukJenis($jenis) {
        return $this->riwayatPembayaran()
                    ->where('jenis_pembayaran', $jenis)
                    ->sum('jumlah_bayar');
    }

    public function getSisaPembayaranAttribute()
{
    // Hitung total biaya
    $totalBiaya = $this->biaya_pendaftaran + $this->biaya_ppdb + $this->biaya_mpls + $this->biaya_awal_tahun;

    // Kembalikan selisih total biaya dan total bayar
    return $totalBiaya - $this->total_bayar;
}

public function getSisaPembayaranFormattedAttribute()
{
    return 'Rp ' . number_format($this->sisa_pembayaran, 0, ',', '.');
}

    public function setSisaPembayaranAttribute($value)
    {
        $this->attributes['sisa_pembayaran'] = $this->biaya_pendaftaran + $this->biaya_ppdb + $this->biaya_awal_tahun + $this->biaya_mpls - $this->total_bayar;
    }
}

