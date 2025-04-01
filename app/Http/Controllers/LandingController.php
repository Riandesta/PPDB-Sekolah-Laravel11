<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Jurusan;
use App\Models\Statistics;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LandingController extends Controller
{
    public function index()
    {
        // Data untuk statistics section
        $statistics = [
            'total_pendaftar' => Statistics::getTotalPendaftar(),
            'total_diterima' => Statistics::getTotalDiterima(),
            'kuota_tersisa' => Statistics::getKuotaTersisa(),
            'pembayaran' => Statistics::getPembayaranStats(),
            'per_jurusan' => Statistics::getStatistikPerJurusan()
        ];

        // Data untuk jurusan section
        $jurusan = Jurusan::all();

        return view('landing.home', compact('statistics', 'jurusan'));
    }

    public function getJurusan()
    {
        $jurusan = Jurusan::with(['kuota'])
            ->whereHas('kuota', function($query) {
                $query->where('is_active', true);
            })
            ->get();

        return response()->json($jurusan);
    }

    public function storeContact(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'subject' => 'required',
            'message' => 'required'
        ]);

        return back()->with('success', 'Pesan Anda telah terkirim.');
    }

    public function cekStatus(Request $request)
    {
        $request->validate([
            'nomor_pendaftaran' => 'required'
        ]);

        $pendaftaran = Pendaftaran::where('nomor_pendaftaran', $request->nomor_pendaftaran)
            ->with(['jurusan', 'administrasi'])
            ->first();

        if (!$pendaftaran) {
            return back()->with('error', 'Nomor pendaftaran tidak ditemukan.');
        }

        return view('landing.cek-status', compact('pendaftaran'));
    }
}
