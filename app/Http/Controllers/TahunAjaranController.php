<?php

namespace App\Http\Controllers;

use App\Models\TahunAjaran;
use App\Services\TahunAjaranService;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    protected $tahunAjaranService;

    public function __construct(TahunAjaranService $tahunAjaranService)
    {
        $this->tahunAjaranService = $tahunAjaranService;
    }

    public function index()
    {
        // Menggunakan withCount untuk menghitung jumlah pendaftaran
        $tahunAjarans = TahunAjaran::withCount('pendaftarans')
            ->orderBy('tahun_mulai', 'desc')
            ->get();

        return view('tahun-ajaran.index', compact('tahunAjarans'));
    }
    public function destroy(TahunAjaran $tahunAjaran)
    {
        try {
            // Check if there are related records
            if (
                $tahunAjaran->pendaftarans()->exists() ||
                $tahunAjaran->kelas()->exists() ||
                $tahunAjaran->administrasi()->exists()
            ) {
                return redirect()
                    ->route('tahun-ajaran.index')
                    ->with('error', 'Tidak dapat menghapus tahun ajaran karena masih memiliki data terkait.');
            }

            $tahunAjaran->delete();

            return redirect()
                ->route('tahun-ajaran.index')
                ->with('success', 'Tahun ajaran berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()
                ->route('tahun-ajaran.index')
                ->with('error', 'Terjadi kesalahan saat menghapus tahun ajaran.' . $e->getMessage());
        }
    }


    public function create()
    {
        $tahunAjaran = new TahunAjaran();
        return view('tahun-ajaran.c_form', compact('tahunAjaran'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateTahunAjaran($request);

        try {
            $validated['is_active'] = $request->has('is_active');
            $this->tahunAjaranService->store($validated);

            return redirect()
                ->route('tahun-ajaran.index')
                ->with('success', 'Tahun ajaran berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan tahun ajaran: ' . $e->getMessage());
        }
    }

    public function show(TahunAjaran $tahunAjaran)
    {
        // Load pendaftarans relationship dengan data terkait
        $tahunAjaran->load(['pendaftarans' => function ($query) {
            $query->with(['jurusan', 'administrasi'])
                ->latest();
        }]);

        return view('tahun-ajaran.show', compact('tahunAjaran'));
    }

    public function edit($id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);
        return view('tahun-ajaran.e_form', compact('tahunAjaran'));
    }


    public function update(Request $request, TahunAjaran $tahunAjaran)
    {
        $validated = $this->validateTahunAjaran($request);

        try {
            $validated['is_active'] = $request->has('is_active');
            $this->tahunAjaranService->update($tahunAjaran, $validated);

            return redirect()
                ->route('tahun-ajaran.index')
                ->with('success', 'Tahun ajaran berhasil diperbarui');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui tahun ajaran: ' . $e->getMessage());
        }
    }

    private function validateTahunAjaran(Request $request)
    {
        return $request->validate([
            'tahun_mulai' => [
                'required',
                'integer',
                'between:2000,2099'
            ],
            'tahun_selesai' => [
                'required',
                'integer',
                'between:2000,2099',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value != $request->tahun_mulai + 1) {
                        $fail('Tahun selesai harus tahun mulai + 1');
                    }
                },
            ],
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'biaya_pendaftaran' => 'required|numeric|min:0',
            'biaya_ppdb' => 'required|numeric|min:0',
            'biaya_mpls' => 'required|numeric|min:0',
            'biaya_awal_tahun' => 'required|numeric|min:0'
        ]);
    }
}
