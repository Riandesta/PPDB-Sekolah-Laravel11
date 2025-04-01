<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\Pendaftaran;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\PendaftaranService;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\PendaftaranRequest;
use Yajra\DataTables\Contracts\DataTable;
use Yajra\DataTables\Facades\DataTables;

class PendaftaranController extends Controller
{
    protected $pendaftaranService;

    public function __construct(PendaftaranService $pendaftaranService)
    {
        $this->pendaftaranService = $pendaftaranService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Pendaftaran::with(['jurusan', 'administrasi'])
            ->orderBy('created_at', 'desc')
            ;


            if ($request->filled('jurusan')) {
                $query->whereHas('jurusan', function($q) use ($request) {
                    $q->where('nama_jurusan', $request->jurusan);
                });
            }

            if ($request->filled('status')) {
                $query->where('status_seleksi', $request->status);
            }

            if ($request->filled('pembayaran')) {
                $query->whereHas('administrasi', function($q) use ($request) {
                    $q->where('status_pembayaran', $request->pembayaran);
                });
            }

            if ($request->filled('tahun_ajaran')) {
                $query->where('tahun_ajaran_id', $request->tahun_ajaran);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $actionBtn = '
                    <span class="dropdown">
                        <button class="btn dropdown-toggle align-text-top btn-sm"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">
                            Actions
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="#"
                               data-bs-toggle="modal"
                               data-bs-target="#detailModal'.$row->id.'"
                               title="Lihat detail pendaftar">
                                <i class="fas fa-eye me-2"></i> View
                            </a>
                            <a class="dropdown-item"
                               href="'.route('pendaftaran.edit', $row->id).'"
                               title="Edit pendaftar">
                                <i class="fas fa-edit me-2"></i> Edit
                            </a>
                            <form action="'.route('pendaftaran.destroy', $row->id).'"
                                  method="POST"
                                  onsubmit="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                                '.csrf_field().'
                                '.method_field('DELETE').'
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-trash me-2"></i> Delete
                                </button>
                            </form>
                        </div>
                    </span>';

                    return $actionBtn;
                })
                ->addColumn('status_badge', function($row) {
                    $badgeClass = match($row->status_seleksi) {
                        'Lulus' => 'bg-success',
                        'Pending' => 'bg-warning',
                        default => 'bg-danger'
                    };
                    return '<span class="badge text-white '.$badgeClass.'">'.$row->status_seleksi.'</span>';
                })
                ->addColumn('pembayaran_badge', function($row) {
                    $badgeClass = $row->administrasi->status_pembayaran === 'Lunas' ? 'bg-success' : 'bg-warning';
                    return '<span class="badge text-white '.$badgeClass.'">'.$row->administrasi->status_pembayaran.'</span>';
                })
                ->rawColumns(['action', 'status_badge', 'pembayaran_badge'])
                ->make(true);
        }

        $tahunAjarans = TahunAjaran::all();
        $jurusans = Jurusan::all();
        $pendaftars = Pendaftaran::with(['jurusan', 'administrasi'])->get();
        return view('pendaftaran.index', compact('jurusans', 'pendaftars', 'tahunAjarans'));
    }



    public function create()
{
    $tahunAjaran = TahunAjaran::where('is_active', true)->first();
    if (!$tahunAjaran) {
        return back()->with('error', 'Tidak ada tahun ajaran yang aktif');
    }

    $jurusans = Jurusan::all();
    return view('pendaftaran.form', compact('jurusans', 'tahunAjaran'));
}


    // PendaftaranController.php
public function store(PendaftaranRequest $request, PendaftaranService $service)
{
    try {
        DB::beginTransaction();

        // Validasi tahun ajaran aktif
        $tahunAjaran = TahunAjaran::where('is_active', true)->first();
        if (!$tahunAjaran) {
            return back()->with('error', 'Tidak ada tahun ajaran aktif');
        }


        // Proses pendaftaran
        $pendaftar = $service->prosesPendaftaran($request->validated());

        DB::commit();
        return redirect()
            ->route('pendaftaran.index')
            ->with('success', 'Pendaftaran berhasil diproses');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }

}


    public function show(Pendaftaran $pendaftaran)
    {
        try {
            $pendaftaran->load(['jurusan', 'administrasi', 'tahunAjaran']);
            return response()->json([
                'success' => true,
                'data' => $pendaftaran
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    public function edit(Pendaftaran $pendaftaran)
    {

        $tahunAjaran = TahunAjaran::where('is_active', true)->first();
        $jurusans = Jurusan::all();

        return view('pendaftaran.form', [
            'pendaftaran' => $pendaftaran,
            'jurusans' => $jurusans,
            'tahunAjaran' => $tahunAjaran,
        ]);
    }



    public function update(
        PendaftaranRequest $request,
        PendaftaranService $service,
        Pendaftaran $pendaftaran
    ) {
        try {
            DB::beginTransaction();

            if ($request->hasFile('foto')) {
                // Hapus foto lama
                if ($pendaftaran->foto) {
                    Storage::delete('public/foto_siswa/' . basename($pendaftaran->foto));
                }

                $path = $request->file('foto')->store('public/foto_siswa');
                $data['foto'] = Storage::url($path);
            }

            $pendaftaran->update($request->validated());

            DB::commit();
            return redirect()
                ->route('pendaftaran.index')
                ->with('success', 'Data berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(Pendaftaran $pendaftaran)
    {
        try {
            DB::beginTransaction();

            // Hapus foto jika ada
            if ($pendaftaran->foto) {
                $this->pendaftaranService->deleteFotoIfExists($pendaftaran->foto);
            }

            $pendaftaran->delete();

            DB::commit();
            return redirect()
                ->route('pendaftaran.index')
                ->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
