<?php

namespace App\Services;

use App\Models\Administrasi;
use App\Models\RiwayatPembayaran;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MidtransService
{
    protected $isProduction;
    protected $serverKey;
    protected $clientKey;

    public function __construct()
    {
        // Set Midtrans configuration
        $this->isProduction = config('services.midtrans.is_production');
        $this->serverKey = config('services.midtrans.server_key');
        $this->clientKey = config('services.midtrans.client_key');

        // Set Midtrans configuration
        \Midtrans\Config::$serverKey = $this->serverKey;
        \Midtrans\Config::$isProduction = $this->isProduction;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;
    }

    /**
     * Create Midtrans Snap Token for payment
     *
     * @param Administrasi $administrasi
     * @param array $data
     * @return array
     */
    public function createTransaction(Administrasi $administrasi, array $data)
    {
        try {
            // Generate order ID
            $orderId = 'PPDB-' . time() . '-' . $administrasi->id;

            // Get selected payment types
            $selectedTypes = $data['jenis_pembayaran'];
            $paymentDetails = [];
            $totalAmount = 0;

            // Create items array for Midtrans
            $items = [];

            // Add items based on selected payment types
            foreach ($selectedTypes as $type) {
                $biayaField = 'biaya_' . $type;
                $sisaField = $administrasi->$biayaField - $administrasi->totalBayarUntukJenis($type);

                // If requested amount is less than or equal to remaining amount
                $amount = min($data['jumlah_bayar'], $sisaField);

                $items[] = [
                    'id' => 'bayar-' . $type,
                    'price' => $amount,
                    'quantity' => 1,
                    'name' => 'Pembayaran ' . ucfirst($type),
                ];

                $totalAmount += $amount;
                $paymentDetails[$type] = $amount;
            }

            // If jumlah_bayar exceeds total sisa for selected types, adjust
            if ($data['jumlah_bayar'] > $totalAmount) {
                $totalAmount = $data['jumlah_bayar'];
            }

            // Create transaction params
            $transactionDetails = [
                'order_id' => $orderId,
                'gross_amount' => $totalAmount,
            ];

            // Customer details
            $customerDetails = [
                'first_name' => $administrasi->pendaftaran->nama,
                'email' => $administrasi->pendaftaran->email ?? 'noemail@example.com',
                'phone' => $administrasi->pendaftaran->no_telp_ortu,
            ];

            // Create transaction parameters
            $params = [
                'transaction_details' => $transactionDetails,
                'customer_details' => $customerDetails,
                'item_details' => $items,
            ];

            // Get Snap Token
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            // Create initial payment record with pending status
            $pembayaran = RiwayatPembayaran::create([
                'administrasi_id' => $administrasi->id,
                'no_pembayaran' => $orderId,
                'tanggal_bayar' => now(),
                'jenis_pembayaran' => $selectedTypes[0], // Primary payment type
                'jumlah_bayar' => $totalAmount,
                'metode_pembayaran' => 'midtrans',
                'status' => 'pending',
                'keterangan' => 'Pembayaran via Midtrans: ' . implode(', ', array_map('ucfirst', $selectedTypes)),
            ]);

            // Store payment details in session for later use
            session(['midtrans_payment' => [
                'order_id' => $orderId,
                'administrasi_id' => $administrasi->id,
                'payment_details' => $paymentDetails,
                'total_amount' => $totalAmount,
                'selected_types' => $selectedTypes,
            ]]);

            return [
                'snap_token' => $snapToken,
                'order_id' => $orderId,
                'pembayaran_id' => $pembayaran->id,
                'status' => 'success',
            ];

        } catch (\Exception $e) {
            Log::error('Midtrans Error: ' . $e->getMessage());

            return [
                'status' => 'error',
                'message' => 'Gagal memproses pembayaran: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Handle notification from Midtrans
     *
     * @param array $notificationData
     * @return array
     */
    public function handleNotification($notificationData)
    {
        try {
            $transaction = $notificationData;

            // For production, use this to get transaction status
            // $transaction = \Midtrans\Transaction::status($notificationData->order_id);

            $orderId = $transaction['order_id'];
            $statusCode = $transaction['status_code'];
            $transactionStatus = $transaction['transaction_status'];
            $fraudStatus = $transaction['fraud_status'] ?? null;
            $grossAmount = $transaction['gross_amount'];

            // Get payment record by order ID
            $pembayaran = RiwayatPembayaran::where('no_pembayaran', $orderId)->first();

            if (!$pembayaran) {
                Log::error('Payment not found: ' . $orderId);
                return [
                    'status' => 'error',
                    'message' => 'Payment not found',
                ];
            }

            $administrasi = Administrasi::find($pembayaran->administrasi_id);

            if (!$administrasi) {
                Log::error('Administrasi not found for payment: ' . $orderId);
                return [
                    'status' => 'error',
                    'message' => 'Administrasi not found',
                ];
            }

            // Get payment details from session or database
            $paymentData = session('midtrans_payment');
            $selectedTypes = $paymentData['selected_types'] ?? [$pembayaran->jenis_pembayaran];
            $paymentDetails = $paymentData['payment_details'] ?? [];

            // Handle transaction status
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    // Set payment status to pending
                    $pembayaran->status = 'pending';
                    $pembayaran->keterangan = 'Pembayaran masih dalam peninjauan.';
                } else if ($fraudStatus == 'accept') {
                    // Set payment status to success
                    $this->processSuccessfulPayment($administrasi, $pembayaran, $selectedTypes, $grossAmount);
                }
            } else if ($transactionStatus == 'settlement') {
                // Set payment status to success
                $this->processSuccessfulPayment($administrasi, $pembayaran, $selectedTypes, $grossAmount);
            } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
                // Set payment status to failed
                $pembayaran->status = 'failed';
                $pembayaran->keterangan = 'Pembayaran ' . $transactionStatus . '.';
                $pembayaran->save();
            } else if ($transactionStatus == 'pending') {
                // Set payment status to pending
                $pembayaran->status = 'pending';
                $pembayaran->keterangan = 'Pembayaran menunggu pembayaran.';
                $pembayaran->save();
            }

            return [
                'status' => 'success',
                'message' => 'Notification processed',
                'data' => [
                    'order_id' => $orderId,
                    'transaction_status' => $transactionStatus,
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Error handling Midtrans notification: ' . $e->getMessage());

            return [
                'status' => 'error',
                'message' => 'Error processing notification: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Process successful payment
     *
     * @param Administrasi $administrasi
     * @param RiwayatPembayaran $pembayaran
     * @param array $selectedTypes
     * @param float $grossAmount
     * @return void
     */
    private function processSuccessfulPayment($administrasi, $pembayaran, $selectedTypes, $grossAmount)
    {
        $pembayaran->status = 'success';
        $pembayaran->keterangan = 'Pembayaran berhasil melalui Midtrans.';
        $pembayaran->save();

        // Update administrasi total payment
        $administrasi->total_bayar += $grossAmount;

        // Update payment status for each type
        foreach ($selectedTypes as $type) {
            $statusField = 'is_' . $type . '_lunas';
            $biayaField = 'biaya_' . $type;

            if (!$administrasi->$statusField && $administrasi->totalBayarUntukJenis($type) >= $administrasi->$biayaField) {
                $administrasi->$statusField = true;
                $tanggalField = 'tanggal_bayar_' . $type;
                $administrasi->$tanggalField = now();
            }
        }

        // Update total payment status
        $totalBiaya = $administrasi->biaya_pendaftaran + $administrasi->biaya_ppdb + $administrasi->biaya_mpls + $administrasi->biaya_awal_tahun;
        $administrasi->status_pembayaran = ($administrasi->total_bayar >= $totalBiaya) ? 'Lunas' : 'Belum Lunas';
        $administrasi->sisa_pembayaran = $totalBiaya - $administrasi->total_bayar;

        // Save changes
        $administrasi->save();
    }

    /**
     * Get client key for frontend
     *
     * @return string
     */
    public function getClientKey()
    {
        return $this->clientKey;
    }
}
