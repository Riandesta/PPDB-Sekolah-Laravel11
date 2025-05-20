<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatPembayaran extends Model
{
    use HasFactory;

    protected $table = 'riwayat_pembayaran';

    protected $fillable = [
        'administrasi_id',
        'no_pembayaran',
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
        'jumlah_bayar' => 'decimal:2',
    ];

    protected $appends = [
        'jumlah_bayar_formatted'
    ];

    // Relationships
    public function administrasi()
    {
        return $this->belongsTo(Administrasi::class);
    }

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class, 'pendaftaran_id');
    }

    // Get formatted amount
    public function getJumlahBayarFormattedAttribute()
    {
        return 'Rp ' . number_format($this->jumlah_bayar, 0, ',', '.');
    }

    // Model hooks - set default values before saving
    protected static function boot()
    {
        parent::boot();

        // Auto-generate payment number if not set
        static::creating(function ($model) {
            if ($model->no_pembayaran === 'auto' || empty($model->no_pembayaran)) {
                $prefix = 'PAY';
                $timestamp = now()->format('YmdHis');
                $random = mt_rand(1000, 9999);
                $model->no_pembayaran = "{$prefix}-{$timestamp}-{$random}";
            }
        });
    }

    // Scope for successful payments
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    // Scope for pending payments
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope for failed payments
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    // Get payment status badge HTML
    public function getStatusBadgeAttribute()
    {
        $badgeClass = 'bg-secondary';

        if ($this->status === 'success') {
            $badgeClass = 'bg-success';
        } elseif ($this->status === 'pending') {
            $badgeClass = 'bg-warning';
        } elseif ($this->status === 'failed') {
            $badgeClass = 'bg-danger';
        }

        return '<span class="badge text-white ' . $badgeClass . '">' . ucfirst($this->status) . '</span>';
    }
}
