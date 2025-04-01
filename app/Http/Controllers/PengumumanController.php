<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
public function index()
{
    $pengumuman = Pendaftaran::with(['jurusan', 'kelas', 'administrasi'])
        ->where('status_seleksi', '!=', 'Pending')
        ->orderBy('jurusan_id')
        ->orderBy('nama')
        ->get();

    return view('pengumuman.index', compact('pengumuman'));
}


}