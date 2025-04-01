<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendaftaran extends Model
{
    use HasFactory;

    protected $table = 'pendaftarans';

    protected $fillable = [
        'daftar_id',
        'NISN',
        'nama',
        'alamat',
        'tgl_lahir',
        'tmp_lahir',
        'jenis_kelamin',
        'agama',
        'asal_sekolah',
        'nama_ortu',
        'pekerjaan_ortu',
        'no_telp_ortu',
        'foto',
        'jurusan_id',
        'tahun_ajaran_id',
        'nilai_semester_1',
        'nilai_semester_2',
        'nilai_semester_3',
        'nilai_semester_4',
        'nilai_semester_5',
        'rata_rata_nilai',
        'status_seleksi'
    ];

    protected $casts = [
        'tgl_lahir' => 'datetime',
    ];



    public function administrasi()
    {
        return $this->hasOne(Administrasi::class, 'pendaftaran_id');
    }

    public function jurusan()
{
    return $this->belongsTo(Jurusan::class);
}

    public function riwayat_pembayaran() {
        return $this->hasMany(RiwayatPembayaran::class, 'pendaftaran_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }
    public function hitungRataRata()
    {
        $nilai = [
            $this->nilai_semester_1,
            $this->nilai_semester_2,
            $this->nilai_semester_3,
            $this->nilai_semester_4,
            $this->nilai_semester_5
        ];

        // Filter out null values
        $nilai_valid = array_filter($nilai, function ($value) {
            return !is_null($value);
        });

        // Calculate average if there are valid values
        if (count($nilai_valid) > 0) {
            return array_sum($nilai_valid) / count($nilai_valid);
        }

        return 0; // Return 0 if no valid values
    }
}
