<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Administrasi;
use App\Models\RiwayatPembayaran;
use App\Services\MidtransService;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Get Snap Token for payment
     *
     * @param Request $request
     * @param Administrasi $administrasi
     * @return \Illuminate\Http\Response
     */
    public function getSnapToken(Request $request, Administrasi $administrasi)
    {
        try {
            $validatedData = $request->validate([
                'jenis_pembayaran' => 'required|array',
                'jenis_pembayaran.*' => 'in:pendaftaran,ppdb,mpls,awal_tahun',
                'jumlah_bayar' => 'required|numeric|min:1',
            ]);

            $result = $this->midtransService->createTransaction($administrasi, $validatedData);

            if ($result['status'] === 'success') {
                return response()->json([
                    'status' => 'success',
                    'snap_token' => $result['snap_token'],
                    'order_id' => $result['order_id'],
                    'client_key' => $this->midtransService->getClientKey()
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message']
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error creating Midtrans transaction: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memproses pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle notification from Midtrans
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function notification(Request $request)
    {
        try {
            $notificationData = $request->all();

            // Log the notification for debugging
            Log::info('Midtrans Notification:', $notificationData);

            // Verify signature
            $orderId = $notificationData['order_id'];
            $statusCode = $notificationData['status_code'];
            $grossAmount = $notificationData['gross_amount'];
            $serverKey = config('services.midtrans.server_key');
            $signature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

            if ($signature !== $notificationData['signature_key']) {
                Log::warning('Invalid signature for order: ' . $orderId);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid signature'
                ], 403);
            }

            // Process the notification
            $result = $this->midtransService->handleNotification($notificationData);

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error handling Midtrans notification: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Error processing notification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle payment finish redirect from Midtrans
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function finish(Request $request)
    {
        try {
            $orderId = $request->input('order_id');
            $result = $request->input('transaction_status');

            // Get payment record
            $pembayaran = RiwayatPembayaran::where('no_pembayaran', $orderId)->first();

            if (!$pembayaran) {
                return redirect()->route('administrasi.pembayaran.index')
                    ->with('error', 'Pembayaran tidak ditemukan.');
            }

            $administrasi = Administrasi::find($pembayaran->administrasi_id);

            if (!$administrasi) {
                return redirect()->route('administrasi.pembayaran.index')
                    ->with('error', 'Data administrasi tidak ditemukan.');
            }

            // Show appropriate message based on result
            if ($result == 'settlement' || $result == 'capture') {
                return redirect()->route('administrasi.pembayaran.struk', $administrasi->id)
                    ->with('success', 'Pembayaran berhasil!');
            } else {
                return redirect()->route('administrasi.pembayaran.detail', $administrasi->id)
                    ->with('info', 'Status pembayaran: ' . $result);
            }
        } catch (\Exception $e) {
            Log::error('Error handling finish URL: ' . $e->getMessage());

            return redirect()->route('administrasi.pembayaran.index')
                ->with('error', 'Terjadi kesalahan saat memproses hasil pembayaran.');
        }
    }

    /**
     * Handle unfinished payment redirect from Midtrans
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function unfinish(Request $request)
    {
        $orderId = $request->input('order_id');
        $pembayaran = RiwayatPembayaran::where('no_pembayaran', $orderId)->first();

        if (!$pembayaran) {
            return redirect()->route('administrasi.pembayaran.index')
                ->with('error', 'Pembayaran tidak ditemukan.');
        }

        $administrasi = Administrasi::find($pembayaran->administrasi_id);

        return redirect()->route('administrasi.pembayaran.detail', $administrasi->id)
            ->with('warning', 'Pembayaran belum selesai. Anda dapat mencoba kembali atau memilih metode pembayaran lain.');
    }

    /**
     * Handle error redirect from Midtrans
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function error(Request $request)
    {
        $orderId = $request->input('order_id');
        $pembayaran = RiwayatPembayaran::where('no_pembayaran', $orderId)->first();

        if (!$pembayaran) {
            return redirect()->route('administrasi.pembayaran.index')
                ->with('error', 'Pembayaran tidak ditemukan.');
        }

        $administrasi = Administrasi::find($pembayaran->administrasi_id);

        return redirect()->route('administrasi.pembayaran.detail', $administrasi->id)
            ->with('error', 'Pembayaran gagal. Silakan coba kembali atau hubungi administrator.');
    }

    /**
     * Check payment status
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request)
    {
        try {
            $orderId = $request->input('order_id');

            // Get status from Midtrans API
            $status = \Midtrans\Transaction::status($orderId);

            return response()->json([
                'status' => 'success',
                'data' => $status
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking payment status: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memeriksa status pembayaran.'
            ], 500);
        }
    }
}
