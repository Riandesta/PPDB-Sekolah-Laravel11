<?php

namespace App\Models;

use App\Models\Pendaftaran;
use Illuminate\Support\Str;
use App\Models\Administrasi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// Model: RiwayatPembayaran.php
class RiwayatPembayaran extends Model
{
    protected $table = 'riwayat_pembayaran';

    protected $fillable = [
        'administrasi_id',
        'pendaftaran_id',
        'tanggal_bayar',
        'jenis_pembayaran',
        'jumlah_bayar',
        'metode_pembayaran',
        'bukti_pembayaran',
        'status',
        'keterangan'
    ];

    protected $casts = [
        'tanggal_bayar' => 'datetime',
        'jumlah_bayar' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Generate UUID untuk no_pembayaran
            $model->no_pembayaran = Str::uuid();
        });
    }


    public function administrasi()
    {
        return $this->belongsTo(Administrasi::class);
    }

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class, 'pendaftaran_id');
    }
}
