<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Contracts\View\View;

class AbsensiExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $kelas;

    public function __construct($kelas)
    {
        $this->kelas = $kelas->load(['jurusan', 'pendaftaran' => function($query) {
            $query->orderBy('nama', 'asc');
        }]);
    }

    public function view(): View
    {
        return view('exports.absensi', [
            'kelas' => $this->kelas,
            'siswa' => $this->kelas->pendaftaran
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]], // Judul
            2 => ['font' => ['bold' => true]], // Sub judul
            3 => ['font' => ['bold' => true]], // Tahun ajaran
            5 => ['font' => ['bold' => true]], // Header tabel
        ];
    }
}
