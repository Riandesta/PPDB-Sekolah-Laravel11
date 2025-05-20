<?php

namespace App\Http\Controllers;

use Dompdf\Dompdf;
use App\Models\Administrasi;
use App\Models\RiwayatPembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\PembayaranService;
use App\Services\MidtransService;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;

class AdministrasiController extends Controller
{
    protected $pembayaranService;
    protected $midtransService;

    public function __construct(PembayaranService $pembayaranService, MidtransService $midtransService)
    {
        $this->pembayaranService = $pembayaranService;
        $this->midtransService = $midtransService;
    }

    public function index()
    {
        return view('administrasi.pembayaran.index');
    }

    public function data()
    {
        $administrasis = Administrasi::with(['pendaftaran.jurusan'])
            ->orderBy('created_at', 'desc')
            ->select('administrasis.*');

        return DataTables::of($administrasis)
            ->addIndexColumn()
            ->addColumn('total_bayar_formatted', function ($row) {
                return 'Rp ' . number_format($row->total_bayar, 0, ',', '.');
            })
            ->addColumn('sisa_pembayaran_formatted', function ($row) {
                return 'Rp ' . number_format($row->sisa_pembayaran, 0, ',', '.');
            })
            ->addColumn('status_badge', function ($row) {
                $badgeClass = $row->status_pembayaran == 'Lunas' ? 'bg-success' : 'bg-warning';
                return '<span class="badge text-white ' . $badgeClass . '">' . $row->status_pembayaran . '</span>';
            })
            ->addColumn('action', function ($row) {
                $html = '<div class="btn-group">';
                if ($row->status_pembayaran != 'Lunas') {
                    $html .= '<a href="' . route('administrasi.pembayaran.bayar', $row->id) . '"
                                    class="btn btn-primary btn-sm">
                                    <i class="fas fa-money-bill"></i> Bayar
                                </a>';
                }
                $html .= '<a href="' . route('administrasi.pembayaran.detail', $row->id) . '"
                                class="btn btn-info btn-sm">
                                <i class="fas fa-info-circle"></i> Detail
                            </a>
                        </div>';
                return $html;
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    public function show(Administrasi $administrasi)
    {
        $administrasi->load(['pendaftaran.jurusan', 'riwayatPembayaran' => function($query) {
            $query->orderBy('created_at', 'desc');
        }]);

        return view('administrasi.pembayaran.detail', compact('administrasi'));
    }

    public function create(Administrasi $administrasi)
    {
        // Add Midtrans client key to pass to the view
        $midtransClientKey = config('services.midtrans.client_key');

        return view('administrasi.pembayaran.bayar', compact('administrasi', 'midtransClientKey'));
    }

    public function store(Request $request, Administrasi $administrasi)
    {
        $request->validate([
            'jenis_pembayaran' => 'required|array',
            'jenis_pembayaran.*' => 'in:pendaftaran,ppdb,mpls,awal_tahun',
            'jumlah_bayar' => 'required|numeric|min:1',
            'metode_pembayaran' => 'required|in:tunai,transfer,midtrans',
            'bukti_pembayaran' => 'required_if:metode_pembayaran,transfer|image|max:2048'
        ]);

        try {
            DB::beginTransaction();

            // If payment method is Midtrans, handle it differently
            if ($request->metode_pembayaran === 'midtrans') {
                // Redirect to the page with Midtrans Snap
                DB::commit();
                return redirect()->route('administrasi.pembayaran.midtrans', $administrasi->id)
                    ->with('payment_data', $request->all());
            }

            // For traditional payment methods (cash/transfer)
            $pembayaran = $this->pembayaranService->prosesPembayaran(
                $administrasi,
                $request->all()
            );

            DB::commit();

            // Generate struk pembayaran
            if ($pembayaran) {
                // Load related data for the administrasi
                $administrasi->load(['pendaftaran', 'pendaftaran.jurusan', 'riwayatPembayaran' => function($query) {
                    $query->latest()->first();
                }]);

                return view('administrasi.pembayaran.struk', compact('administrasi'));
            } else {
                return back()->with('error', 'Pembayaran gagal: Data pembayaran tidak ditemukan.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment processing error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Show Midtrans payment page
     */
    public function midtransPayment(Request $request, Administrasi $administrasi)
    {
        // Get payment data from session
        $paymentData = session('payment_data', $request->all());

        // Client key for Midtrans
        $clientKey = config('services.midtrans.client_key');

        return view('administrasi.pembayaran.midtrans', compact('administrasi', 'paymentData', 'clientKey'));
    }

    public function struk(Administrasi $administrasi)
    {
        // Load the administrasi with related data
        $administrasi->load([
            'pendaftaran',
            'pendaftaran.jurusan',
            'riwayatPembayaran' => function ($query) {
                $query->latest()->first(); // Ambil hanya pembayaran terbaru
            }
        ]);

        return view('administrasi.pembayaran.struk', compact('administrasi'));
    }

    /**
     * Check payment status
     */
    public function checkPaymentStatus(Request $request, $orderId)
    {
        try {
            // Find payment record
            $pembayaran = RiwayatPembayaran::where('no_pembayaran', $orderId)->first();

            if (!$pembayaran) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pembayaran tidak ditemukan'
                ], 404);
            }

            // If payment is done through Midtrans, check with Midtrans API
            if ($pembayaran->metode_pembayaran === 'midtrans') {
                try {
                    $status = \Midtrans\Transaction::status($orderId);
                    return response()->json([
                        'status' => 'success',
                        'payment_status' => $status['transaction_status'],
                        'data' => $status
                    ]);
                } catch (\Exception $e) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Gagal memeriksa status: ' . $e->getMessage(),
                        'payment_status' => $pembayaran->status
                    ]);
                }
            }

            // For regular payments, just return status from database
            return response()->json([
                'status' => 'success',
                'payment_status' => $pembayaran->status
            ]);

        } catch (\Exception $e) {
            Log::error('Error checking payment status: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
