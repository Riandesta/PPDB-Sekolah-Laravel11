<?php
// app/Services/PendaftaranService.php

namespace App\Services;

use App\Models\KuotaPPDB;
use App\Models\Pendaftaran;
use App\Models\TahunAjaran;
use App\Models\Administrasi;
use App\Services\KelasService;
use App\Models\RiwayatPembayaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class PendaftaranService
{
    protected $kelasService;

    public function __construct(KelasService $kelasService)
    {
        $this->kelasService = $kelasService;
    }

    public function prosesPendaftaran($data)
    {
        return DB::transaction(function () use ($data) {
            try {
                // Generate daftar_id
                $data['daftar_id'] = $this->generateDaftarId();

                // Handle upload foto
                if (isset($data['foto'])) {
                    $data['foto'] = $this->handleFotoUpload($data['foto']);
                }

                // Hitung rata-rata nilai
                $data['rata_rata_nilai'] = $this->hitungRataRata([
                    $data['nilai_semester_1'] ?? 0,
                    $data['nilai_semester_2'] ?? 0,
                    $data['nilai_semester_3'] ?? 0,
                    $data['nilai_semester_4'] ?? 0,
                    $data['nilai_semester_5'] ?? 0
                ]);

                // Cek kuota
                $kuota = KuotaPPDB::where('tahun_ajaran_id', $data['tahun_ajaran_id'])
                    ->where('jurusan_id', $data['jurusan_id'])
                    ->first();

                // Tentukan status seleksi
                $data['status_seleksi'] = $this->determineSelectionStatus(
                    $kuota?->isKuotaAvailable() ?? false,
                    $data['rata_rata_nilai']
                );

                // Buat pendaftaran
                $pendaftaran = Pendaftaran::create($data);

                // Buat administrasi
                $administrasi = $this->createAdministrasi($pendaftaran);

                // Proses pembayaran awal dan simpan riwayat pembayaran
                if (isset($data['pembayaran_awal']) && $data['pembayaran_awal'] > 0) {
                    $administrasi = $this->processPembayaranAwal(
                        $administrasi,
                        $data['pembayaran_awal'],
                        $data['metode_pembayaran'] ?? 'tunai', // <-- Teruskan metode pembayaran
                        $data['bukti_pembayaran'] ?? null       // <-- Teruskan bukti pembayaran
                    );

                    // Jika status Lulus dan pembayaran memenuhi syarat, assign ke kelas
                    if (
                        $data['status_seleksi'] === 'Lulus' &&
                        $data['pembayaran_awal'] >= config('ppdb.minimum_pembayaran', 0)
                    ) {
                        $this->kelasService->assignSiswaToPendaftaran($pendaftaran);
                    }
                }

                return $pendaftaran->fresh();
            } catch (\Exception $e) {
                Log::error('Error in prosesPendaftaran: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    private function createAdministrasi($pendaftaran)
    {
        // Ambil tahun ajaran untuk mendapatkan biaya-biaya
        $tahunAjaran = TahunAjaran::findOrFail($pendaftaran->tahun_ajaran_id);

        return Administrasi::create([
            'pendaftaran_id' => $pendaftaran->id,
            'tahun_ajaran_id' => $pendaftaran->tahun_ajaran_id,
            'no_bayar' => 'BYR' . date('Ymd') . str_pad($pendaftaran->id, 4, '0', STR_PAD_LEFT),
            'biaya_pendaftaran' => $tahunAjaran->biaya_pendaftaran,
            'biaya_ppdb' => $tahunAjaran->biaya_ppdb,
            'biaya_mpls' => $tahunAjaran->biaya_mpls,
            'biaya_awal_tahun' => $tahunAjaran->biaya_awal_tahun,
            'total_bayar' => 0,
            'status_pembayaran' => 'Belum Lunas',
            'is_pendaftaran_lunas' => false,
            'is_ppdb_lunas' => false,
            'is_mpls_lunas' => false,
            'is_awal_tahun_lunas' => false
        ]);
    }

    private function hitungRataRata(array $nilai): float
    {
        $nilai_valid = array_filter($nilai, fn($value) => $value > 0);
        return count($nilai_valid) > 0 ? array_sum($nilai_valid) / count($nilai_valid) : 0;
    }

    private function determineSelectionStatus(bool $kuotaAvailable, float $rataRata): string
    {
        if (!$kuotaAvailable) {
            return 'Pending';
        }

        $nilaiMinimum = config('ppdb.nilai_minimum', 75);
        return $rataRata >= $nilaiMinimum ? 'Lulus' : 'Tidak Lulus';
    }

    private function processPembayaranAwal($administrasi, $jumlahBayar, $metodePembayaran, $buktiPembayaran = null)
{
    return DB::transaction(function () use ($administrasi, $jumlahBayar, $metodePembayaran, $buktiPembayaran) {
        // Update total pembayaran
        $administrasi->total_bayar = $jumlahBayar;

        // Hitung total biaya
        $totalBiaya = $administrasi->biaya_pendaftaran +
            $administrasi->biaya_ppdb +
            $administrasi->biaya_mpls +
            $administrasi->biaya_awal_tahun;

        // Update status komponen pembayaran
        if ($jumlahBayar >= $administrasi->biaya_pendaftaran) {
            $administrasi->is_pendaftaran_lunas = true;
            $administrasi->tanggal_bayar_pendaftaran = now();

            $sisaBayar = $jumlahBayar - $administrasi->biaya_pendaftaran;

            if ($sisaBayar >= $administrasi->biaya_ppdb) {
                $administrasi->is_ppdb_lunas = true;
                $administrasi->tanggal_bayar_ppdb = now();
                $sisaBayar -= $administrasi->biaya_ppdb;

                if ($sisaBayar >= $administrasi->biaya_mpls) {
                    $administrasi->is_mpls_lunas = true;
                    $administrasi->tanggal_bayar_mpls = now();
                    $sisaBayar -= $administrasi->biaya_mpls;

                    if ($sisaBayar >= $administrasi->biaya_awal_tahun) {
                        $administrasi->is_awal_tahun_lunas = true;
                        $administrasi->tanggal_bayar_awal_tahun = now();
                    }
                }
            }
            // return $administrasi; <-- Hapus ini
        }

        // Simpan riwayat pembayaran (pindahkan ke luar blok if)
        $this->createRiwayatPembayaran($administrasi, $jumlahBayar, $metodePembayaran, $buktiPembayaran);

        $administrasi->status_pembayaran = ($jumlahBayar >= $totalBiaya) ? 'Lunas' : 'Belum Lunas';
        $administrasi->save();

        return $administrasi;
    });
}



    private function createRiwayatPembayaran($administrasi, $jumlahBayar, $metodePembayaran, $buktiPembayaran = null)
    {
        $riwayatData = [
            'administrasi_id' => $administrasi->id,
            'jumlah_bayar' => $jumlahBayar,
            'metode_pembayaran' => $metodePembayaran,
            'tanggal_bayar' => now(),
            'keterangan' => 'Pembayaran awal pendaftaran'
        ];

        // Handle upload bukti pembayaran
        if ($buktiPembayaran) {
            $riwayatData['bukti_pembayaran'] = $this->handleBuktiPembayaranUpload($buktiPembayaran);
        }

        RiwayatPembayaran::create($riwayatData);
    }


    private function handleFotoUpload($file)
    {
        try {
            // Generate nama file yang unik
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Simpan file ke storage dan dapatkan path relatifnya
            $path = $file->storeAs('foto_siswa', $fileName, 'public');

            // Return path relatif yang akan disimpan di database
            return $path;
        } catch (\Exception $e) {
            Log::error('Error uploading foto: ' . $e->getMessage());
            throw new \Exception('Gagal mengupload foto');
        }
    }

    private function handleBuktiPembayaranUpload($file)
    {
        try {
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('bukti_pembayaran', $fileName, 'public');
            return $path;
        } catch (\Exception $e) {
            Log::error('Error uploading bukti pembayaran: ' . $e->getMessage());
            throw new \Exception('Gagal mengupload bukti pembayaran');
        }
    }

    public function deleteFotoIfExists($fotoPath)
    {
        if ($fotoPath && Storage::disk('public')->exists($fotoPath)) {
            Storage::disk('public')->delete($fotoPath);
        }
    }
    private function generateDaftarId()
    {
        $tahun = date('Y');
        $bulan = date('m');
        $prefix = "PPDB{$tahun}{$bulan}";

        // Ambil nomor urut terakhir
        $lastNumber = Pendaftaran::where('daftar_id', 'like', $prefix . '%')
            ->orderBy('daftar_id', 'desc')
            ->first();

        if ($lastNumber) {
            $increment = intval(substr($lastNumber->daftar_id, -4)) + 1;
        } else {
            $increment = 1;
        }

        // Format: PPDB202412xxxx (contoh: PPDB2024120001)
        return $prefix . str_pad($increment, 4, '0', STR_PAD_LEFT);
    }

    public function updateStatusSeleksi(Pendaftaran $pendaftaran, string $newStatus)
    {
        $pendaftaran->status_seleksi = $newStatus;
        $pendaftaran->save();

        if ($newStatus === 'Lulus') {
            $this->kelasService->assignSiswaToPendaftaran($pendaftaran);
        } else if ($pendaftaran->kelas_id) {
            $this->kelasService->removeSiswaFromKelas($pendaftaran);
        }
    }

    public function validateJurusanKuota($jurusanId)
    {
        $kuota = KuotaPPDB::where('jurusan_id', $jurusanId)
            ->where('tahun_ajaran_id', $this->getActiveTahunAjaran()->id)
            ->first();

        if (!$kuota || !$kuota->isKuotaAvailable()) {
            throw new \Exception('Kuota untuk jurusan ini sudah penuh');
        }

        return true;
    }

    private function getActiveTahunAjaran()
    {
        $tahunAjaran = TahunAjaran::where('status', 'aktif')->first();
        if (!$tahunAjaran) {
            throw new \Exception('Tidak ada tahun ajaran yang aktif');
        }
        return $tahunAjaran;
    }

    public function updatePendaftaran($pendaftaran, $data)
    {
        return DB::transaction(function () use ($pendaftaran, $data) {
            // Handle upload foto baru jika ada
            if (isset($data['foto'])) {
                // Hapus foto lama jika ada
                if ($pendaftaran->foto) {
                    Storage::delete($pendaftaran->foto);
                }
                $data['foto'] = $data['foto']->store('public/foto-siswa');
            }

            // Hitung rata-rata nilai jika ada perubahan nilai
            if (
                isset($data['nilai_semester_1']) || isset($data['nilai_semester_2']) ||
                isset($data['nilai_semester_3']) || isset($data['nilai_semester_4']) ||
                isset($data['nilai_semester_5'])
            ) {

                $data['rata_rata_nilai'] = $this->hitungRataRata([
                    $data['nilai_semester_1'] ?? $pendaftaran->nilai_semester_1 ?? 0,
                    $data['nilai_semester_2'] ?? $pendaftaran->nilai_semester_2 ?? 0,
                    $data['nilai_semester_3'] ?? $pendaftaran->nilai_semester_3 ?? 0,
                    $data['nilai_semester_4'] ?? $pendaftaran->nilai_semester_4 ?? 0,
                    $data['nilai_semester_5'] ?? $pendaftaran->nilai_semester_5 ?? 0
                ]);
            }
            // Cek kuota jika ada perubahan jurusan
            if (isset($data['jurusan_id']) && $data['jurusan_id'] != $pendaftaran->jurusan_id) {
                $kuota = KuotaPPDB::where('tahun_ajaran_id', $pendaftaran->tahun_ajaran_id)
                    ->where('jurusan_id', $data['jurusan_id'])
                    ->first();

                $data['status_seleksi'] = $this->determineSelectionStatus(
                    $kuota?->isKuotaAvailable() ?? false,
                    $data['rata_rata_nilai'] ?? $pendaftaran->rata_rata_nilai
                );
            }

            // Update data pendaftaran
            $pendaftaran->update($data);

            // Proses pembayaran tambahan jika ada
            if (isset($data['pembayaran_tambahan']) && $data['pembayaran_tambahan'] > 0) {
                $this->processPembayaranAwal(
                    $pendaftaran->administrasi, // <-- Ambil administrasi dari $pendaftaran
                    $pendaftaran->administrasi->total_bayar + $data['pembayaran_tambahan'],
                    'tunai', // <-- Nilai default untuk metode pembayaran
                    null      // <-- Nilai default untuk bukti pembayaran
                );
            }   

            return $pendaftaran->fresh();
        });
    }

    public function deletePendaftaran($pendaftaran)
    {
        return DB::transaction(function () use ($pendaftaran) {
            try {
                // Hapus foto jika ada
                if ($pendaftaran->foto) {
                    Storage::delete($pendaftaran->foto);
                }


                if ($pendaftaran->administrasi) {
                    $pendaftaran->administrasi->delete();
                }

                // Hapus penempatan kelas jika ada
                if ($pendaftaran->kelas) {
                    $this->kelasService->removeSiswaFromKelas($pendaftaran);
                }

                // Hapus pendaftaran
                $pendaftaran->delete();

                return true;
            } catch (\Exception $e) {
                Log::error('Error deleting pendaftaran: ' . $e->getMessage());
                throw new \Exception('Gagal menghapus data pendaftaran');
            }
        });
    }

    // Method helper untuk validasi data sebelum update
    private function validateUpdateData($pendaftaran, $data)
    {
        // Validasi perubahan jurusan
        if (isset($data['jurusan_id']) && $pendaftaran->status_seleksi === 'Lulus') {
            throw new \Exception('Tidak dapat mengubah jurusan untuk pendaftar yang sudah lulus');
        }

        // Validasi perubahan tahun ajaran
        if (isset($data['tahun_ajaran_id']) && $data['tahun_ajaran_id'] !== $pendaftaran->tahun_ajaran_id) {
            throw new \Exception('Tidak dapat mengubah tahun ajaran pendaftaran');
        }


        return true;
    }
}
