<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class AbsensiExport implements FromView
{
    protected $kelas;

    public function __construct($kelas)
    {
        $this->kelas = $kelas;
    }

    public function view(): View
    {
        return view('exports.absensi', [
            'kelas' => $this->kelas
        ]);
    }
}
