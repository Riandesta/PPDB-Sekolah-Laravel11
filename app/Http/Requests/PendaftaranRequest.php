<?php
// app/Http/Requests/PendaftaranRequest.php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PendaftaranRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Izinkan semua user untuk mengakses
    }

    public function rules()
    {

    $pendaftaranId = $this->route('pendaftarans');
        return [
            'NISN' => 
                'required',
                'string',
                'min:10',
                'max:10',


            'daftar_id' => 'nullable|string|unique:pendaftarans,daftar_id',
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'tgl_lahir' => 'required|date',
            'tmp_lahir' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'agama' => 'required|string',
            'asal_sekolah' => 'required|string',
            'nama_ortu' => 'required|string|max:255',
            'pekerjaan_ortu' => 'required|string',
            'no_telp_ortu' => 'required|string',
            'foto' => 'nullable|image|max:2048', 
            'jurusan_id' => 'required|exists:jurusans,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'pembayaran_awal' => 'required|numeric|min:0',
            'metode_pembayaran' => 'nullable|in:tunai,transfer',
            'bukti_pembayaran' => 'required_if:metode_pembayaran,transfer|image|max:2048',

            // Nilai Akademik
            'nilai_semester_1' => 'nullable|numeric|between:0,100',
            'nilai_semester_2' => 'nullable|numeric|between:0,100',
            'nilai_semester_3' => 'nullable|numeric|between:0,100',
            'nilai_semester_4' => 'nullable|numeric|between:0,100',
            'nilai_semester_5' => 'nullable|numeric|between:0,100',

            // Pembayaran
            'pembayaran_awal' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    if ($value > 0 && $value < config('ppdb.minimum_pembayaran', 0)) {
                        $fail('Pembayaran awal minimal Rp. ' . number_format(config('ppdb.minimum_pembayaran'), 0, ',', '.'));
                    }
                },
            ],
        ];
    }

    public function messages()
    {
        return [
            'NISN.required' => 'NISN wajib diisi',
            'NISN.unique' => 'NISN sudah terdaftar',
            'nama.required' => 'Nama lengkap wajib diisi',
            'nama.max' => 'Nama maksimal 255 karakter',
            'alamat.required' => 'Alamat wajib diisi',
            'tgl_lahir.required' => 'Tanggal lahir wajib diisi',
            'tgl_lahir.date' => 'Format tanggal lahir tidak valid',
            'tmp_lahir.required' => 'Tempat lahir wajib diisi',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
            'jenis_kelamin.in' => 'Jenis kelamin tidak valid',
            'agama.required' => 'Agama wajib diisi',
            'asal_sekolah.required' => 'Asal sekolah wajib diisi',
            'nama_ortu.required' => 'Nama orang tua wajib diisi',
            'pekerjaan_ortu.required' => 'Pekerjaan orang tua wajib diisi',
            'no_telp_ortu.required' => 'Nomor telepon orang tua wajib diisi',
            'foto.image' => 'File harus berupa gambar',
            'foto.max' => 'Ukuran foto maksimal 2MB',
            'jurusan_id.required' => 'Jurusan wajib dipilih',
            'jurusan_id.exists' => 'Jurusan tidak valid',
            'tahun_ajaran_id.required' => 'Tahun ajaran wajib dipilih',
            'tahun_ajaran_id.exists' => 'Tahun ajaran tidak valid',
            'pembayaran_awal.required' => 'Jumlah pembayaran wajib diisi',
            'pembayaran_awal.numeric' => 'Jumlah pembayaran harus berupa angka',
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih',
            'bukti_pembayaran.required_if' => 'Bukti pembayaran wajib diupload untuk pembayaran transfer'
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('tgl_lahir')) {
            $this->merge([
                'tgl_lahir' => date('Y-m-d', strtotime($this->tgl_lahir))
            ]);
        }
    }
}
