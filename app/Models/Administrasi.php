<?php

namespace App\Models;

use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Administrasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'pendaftaran_id',
        'no_bayar',
        'tanggal_bayar',
        'biaya_pendaftaran',
        'biaya_ppdb',
        'biaya_awal_tahun',
        'biaya_mpls',
        'total_bayar',
        'status_pembayaran',
        'is_pendaftaran_lunas',
        'is_ppdb_lunas',
        'is_mpls_lunas',
        'is_awal_tahun_lunas',
        'tanggal_bayar_pendaftaran',
        'tanggal_bayar_ppdb',
        'tanggal_bayar_mpls',
        'tanggal_bayar_awal_tahun',
        'metode_pembayaran',
        'bukti_pembayaran',
        'keterangan',
        'sisa_pembayaran',
    ];

    protected $casts = [
        'tanggal_bayar' => 'datetime',
        'tanggal_bayar_pendaftaran' => 'datetime',
        'tanggal_bayar_ppdb' => 'datetime',
        'tanggal_bayar_mpls' => 'datetime',
        'tanggal_bayar_awal_tahun' => 'datetime',
        'is_pendaftaran_lunas' => 'boolean',
        'is_ppdb_lunas' => 'boolean',
        'is_mpls_lunas' => 'boolean',
        'is_awal_tahun_lunas' => 'boolean',
    ];

    protected $appends = [
        'sisa_pembayaran_formatted'
    ];

    // Relationships
    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function riwayatPembayaran(): HasMany
    {
        return $this->hasMany(RiwayatPembayaran::class)->orderBy('created_at', 'desc');
    }

    // Calculate total payment for a specific type
    public function totalBayarUntukJenis($jenis)
    {
        return $this->riwayatPembayaran()
            ->where('jenis_pembayaran', $jenis)
            ->where('status', 'success') // Only count successful payments
            ->sum('jumlah_bayar');
    }

    // Get total remaining payment
    public function getSisaPembayaranAttribute()
    {
        $totalBiaya = $this->biaya_pendaftaran + $this->biaya_ppdb + $this->biaya_mpls + $this->biaya_awal_tahun;
        return $totalBiaya - $this->total_bayar;
    }

    // Get formatted remaining payment
    public function getSisaPembayaranFormattedAttribute()
    {
        return 'Rp ' . number_format($this->sisa_pembayaran, 0, ',', '.');
    }

    // Get latest payment
    public function getLatestPaymentAttribute()
    {
        return $this->riwayatPembayaran()->latest()->first();
    }

    // Check if all components are paid
    public function getIsFullyPaidAttribute()
    {
        return $this->is_pendaftaran_lunas &&
               $this->is_ppdb_lunas &&
               $this->is_mpls_lunas &&
               $this->is_awal_tahun_lunas;
    }

    // Get pending payments
    public function getPendingPaymentsAttribute()
    {
        return $this->riwayatPembayaran()
            ->where('status', 'pending')
            ->get();
    }
    public function updateStatusPembayaran(): void
    {
        $this->sisa_pembayaran = $this->total_biaya - $this->total_bayar;
        $this->status_pembayaran = $this->sisa_pembayaran <= 0 ? 'Lunas' : 'Belum Lunas';
        $this->save();
    }

    public function setSisaPembayaranAttribute($value)
    {
        $this->attributes['sisa_pembayaran'] = $this->biaya_pendaftaran + $this->biaya_ppdb + $this->biaya_awal_tahun + $this->biaya_mpls - $this->total_bayar;
    }
}
