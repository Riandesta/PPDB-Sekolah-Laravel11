<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use App\Exports\AbsensiExport;
use App\Services\KelasService;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf; // Perbaikan import PDF

class KelasController extends Controller
{
    protected $kelasService;

    public function __construct(KelasService $kelasService)
    {
        $this->kelasService = $kelasService;
    }

    public function index()
    {
        $kelasGroup = $this->kelasService->getKelasGroupedByJurusan();
        return view('kelas.index', compact('kelasGroup'));
    }

    public function show(Kelas $kelas)
    {
        $jurusan = Jurusan::all();
        $kelasDetail = $this->kelasService->getKelasDetail($kelas);

        // Debugging data
        if (!$kelasDetail) {
            abort(404, 'Detail kelas tidak ditemukan');
        }

        // Load relasi yang diperlukan
        $kelas->load(['jurusan', 'pendaftaran.administrasi']);

        return view('kelas.show', compact('kelas', 'kelasDetail', 'jurusan'));
    }


    public function exportAbsensi(Kelas $kelas)
    {
        return Excel::download(new AbsensiExport($kelas),
            "absensi_{$kelas->nama_kelas}.xlsx");
    }

    public function printAbsensi(Kelas $kelas)
    {
        $data = compact('kelas');
        return PDF::loadView('kelas.print-absensi', $data)
            ->setPaper('a4', 'landscape')
            ->stream("absensi_{$kelas->nama_kelas}.pdf");
    }
}
