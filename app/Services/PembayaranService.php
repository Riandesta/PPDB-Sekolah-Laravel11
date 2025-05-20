<?php

namespace App\Services;

use App\Models\Administrasi;
use App\Models\RiwayatPembayaran;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PembayaranService
{
    public function prosesPembayaran(Administrasi $administrasi, array $data)
    {
        // Check if payment is using Midtrans
        if (isset($data['metode_pembayaran']) && $data['metode_pembayaran'] === 'midtrans') {
            // Defer processing to Midtrans Service
            return null; // We'll handle this in MidtransController
        }

        // Validasi pembayaran
        $this->validasiPembayaran($administrasi, $data);

        // Upload bukti pembayaran jika ada
        $buktiPembayaran = isset($data['bukti_pembayaran']) ? $this->uploadBuktiPembayaran($data['bukti_pembayaran']) : null;

        // Buat record riwayat pembayaran
        $pembayaran = $this->createRiwayatPembayaran(
            $administrasi,
            $data['jumlah_bayar'],
            $data['metode_pembayaran'],
            $data['jenis_pembayaran'][0], // Simpan jenis utama
            $buktiPembayaran,
            $data['keterangan'] ?? 'Pembayaran manual'
        );

        // Update status pembayaran administrasi
        $this->updateStatusPembayaran($administrasi, $data['jumlah_bayar'], $data['jenis_pembayaran']);

        return $pembayaran; // Return the pembayaran object
    }

    public function createRiwayatPembayaran($administrasi, $jumlahBayar, $metodePembayaran, $jenisPembayaran, $buktiPembayaran = null, $keterangan = null)
    {
        // Generate unique payment number
        $noPembayaran = 'PAY-' . time() . '-' . str_pad($administrasi->id, 4, '0', STR_PAD_LEFT);

        return RiwayatPembayaran::create([
            'administrasi_id' => $administrasi->id,
            'no_pembayaran' => $noPembayaran,
            'jumlah_bayar' => $jumlahBayar,
            'jenis_pembayaran' => $jenisPembayaran,
            'metode_pembayaran' => $metodePembayaran,
            'tanggal_bayar' => now(),
            'keterangan' => $keterangan ?? 'Pembayaran administrasi',
            'bukti_pembayaran' => $buktiPembayaran,
            'status' => 'success' // For manual payment, mark as success immediately
        ]);
    }

    private function validasiPembayaran(Administrasi $administrasi, array $data)
    {
        if (!isset($data['jenis_pembayaran']) || !is_array($data['jenis_pembayaran'])) {
            throw new \Exception('Jenis pembayaran tidak valid.');
        }

        // Validate total amount against all selected payment types
        $totalSisaBiaya = 0;
        foreach ($data['jenis_pembayaran'] as $jenis) {
            $biayaKey = 'biaya_' . $jenis;
            $sisaBiaya = $administrasi->$biayaKey - $administrasi->totalBayarUntukJenis($jenis);
            $totalSisaBiaya += $sisaBiaya;
        }

        if ($data['jumlah_bayar'] > $totalSisaBiaya) {
            throw new \Exception('Jumlah pembayaran melebihi total sisa tagihan.');
        }
    }

    private function uploadBuktiPembayaran($file)
    {
        return $file->store('bukti-pembayaran', 'public');
    }

    public function updateStatusPembayaran(Administrasi $administrasi, $totalPembayaran, $jenisPembayaran)
    {
        try {
            // Add the payment to total
            $administrasi->total_bayar += $totalPembayaran;

            // Calculate how much to allocate to each payment type
            $remainingAmount = $totalPembayaran;

            // Distribute payment to each type sequentially
            foreach ($jenisPembayaran as $jenis) {
                $statusField = 'is_' . $jenis . '_lunas';
                $biayaField = 'biaya_' . $jenis;
                $sisaBiaya = $administrasi->$biayaField - $administrasi->totalBayarUntukJenis($jenis);

                // If this type is not yet fully paid
                if (!$administrasi->$statusField && $sisaBiaya > 0) {
                    // Allocate payment to this type
                    $allocation = min($remainingAmount, $sisaBiaya);

                    // If this payment completes this type
                    if ($administrasi->totalBayarUntukJenis($jenis) + $allocation >= $administrasi->$biayaField) {
                        $administrasi->$statusField = true;
                        $tanggalField = 'tanggal_bayar_' . $jenis;
                        $administrasi->$tanggalField = now();
                    }

                    // Reduce remaining amount
                    $remainingAmount -= $allocation;

                    // If no more funds to allocate, break
                    if ($remainingAmount <= 0) {
                        break;
                    }
                }
            }

            // Update total payment status
            $totalBiaya = $administrasi->biaya_pendaftaran + $administrasi->biaya_ppdb + $administrasi->biaya_mpls + $administrasi->biaya_awal_tahun;
            $administrasi->status_pembayaran = ($administrasi->total_bayar >= $totalBiaya) ? 'Lunas' : 'Belum Lunas';
            $administrasi->sisa_pembayaran = $totalBiaya - $administrasi->total_bayar;

            // Save changes
            $administrasi->save();

            return true;
        } catch (\Exception $e) {
            Log::error('Error updating payment status: ' . $e->getMessage());
            return false;
        }
    }
}
