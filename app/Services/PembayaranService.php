<?php

namespace App\Services;

use App\Models\Administrasi;
use App\Models\RiwayatPembayaran;
use Illuminate\Support\Facades\Storage;

class PembayaranService
{
    public function prosesPembayaran(Administrasi $administrasi, array $data)
{
    // Validasi pembayaran
    $this->validasiPembayaran($administrasi, $data);

    // Upload bukti pembayaran jika ada
    $buktiPembayaran = isset($data['bukti_pembayaran']) ? $this->uploadBuktiPembayaran($data['bukti_pembayaran']) : null;

    // Hitung total pembayaran dari komponen yang dipilih
    $totalPembayaran = $this->hitungTotalPembayaran(
        $administrasi,
        $data['jenis_pembayaran'],
        $data['jumlah_bayar']
    );

    // Buat record riwayat pembayaran
    $pembayaran = $this->createRiwayatPembayaran($administrasi, $data['jumlah_bayar'], $data['metode_pembayaran'], $buktiPembayaran);

    // Update status pembayaran administrasi
    $this->updateStatusPembayaran($administrasi, $totalPembayaran, $data['jenis_pembayaran']);

    return $pembayaran; // Return the pembayaran object
}

private function createRiwayatPembayaran($administrasi, $jumlahBayar, $metodePembayaran, $buktiPembayaran = null)
{
    return RiwayatPembayaran::create([
        'administrasi_id' => $administrasi->id,
        'jumlah_bayar' => $jumlahBayar,
        'metode_pembayaran' => $metodePembayaran,
        'tanggal_bayar' => now(),
        'keterangan' => 'Pembayaran awal pendaftaran',
        'bukti_pembayaran' => $buktiPembayaran,
        'status' => 'success'
    ]);
}

    private function validasiPembayaran(Administrasi $administrasi, array $data)
    {
        if (!isset($data['jenis_pembayaran']) || !is_array($data['jenis_pembayaran'])) {
            throw new \Exception('Jenis pembayaran tidak valid.');
        }

        foreach ($data['jenis_pembayaran'] as $jenis) {
            $biayaKey = 'biaya_' . $jenis;
            $sisaBiaya = $administrasi->$biayaKey - $administrasi->totalBayarUntukJenis($jenis);

            if ($data['jumlah_bayar'] > $sisaBiaya) {
                throw new \Exception('Jumlah pembayaran melebihi sisa tagihan untuk ' . $jenis);
            }
        }
    }

    private function uploadBuktiPembayaran($file)
    {
        return $file->store('bukti-pembayaran', 'public');
    }

    private function updateStatusPembayaran(Administrasi $administrasi, $totalPembayaran, $jenisPembayaran)
    {
        $administrasi->total_bayar += $totalPembayaran;

        foreach ($jenisPembayaran as $jenis) {
            $statusField = 'is_' . $jenis . '_lunas';
            $biayaField = 'biaya_' . $jenis;

            if (!$administrasi->$statusField && $administrasi->totalBayarUntukJenis($jenis) >= $administrasi->$biayaField) {
                $administrasi->$statusField = true;
                $tanggalField = 'tanggal_bayar_' . $jenis;
                $administrasi->$tanggalField = now();
            }
        }

        $totalBiaya = $administrasi->biaya_pendaftaran + $administrasi->biaya_ppdb + $administrasi->biaya_mpls + $administrasi->biaya_awal_tahun;
        $administrasi->status_pembayaran = ($administrasi->total_bayar >= $totalBiaya) ? 'Lunas' : 'Belum Lunas';
        $administrasi->sisa_pembayaran = $totalBiaya - $administrasi->total_bayar;

        $administrasi->save();
    }

    private function hitungTotalPembayaran(Administrasi $administrasi, array $jenisPembayaran, $jumlahBayar)
    {
        return $jumlahBayar;
    }
}
