<?php
namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\Pendaftaran;
use App\Models\TahunAjaran;
use Faker\Factory as Faker;
use App\Models\Administrasi;
use Illuminate\Database\Seeder;

class PendaftaranSeeder extends Seeder
{
    public function run()
    {
        $tahunAjaran = TahunAjaran::where('is_active', true)->first();
        if (!$tahunAjaran) {
            $this->command->error('Tahun Ajaran aktif tidak ditemukan!');
            return;
        }

        // Ambil semua jurusan
        $jurusans = Jurusan::all();
        if ($jurusans->isEmpty()) {
            $this->command->error('Jurusan tidak ditemukan!');
            return;
        }

        // Iterasi untuk setiap jurusan
        foreach ($jurusans as $jurusan) {
            // Dapatkan kelas untuk jurusan ini
            $kelas = Kelas::where('jurusan_id', $jurusan->id)
                         ->where('tahun_ajaran_id', $tahunAjaran->id)
                         ->get();

            if ($kelas->isEmpty()) {
                $this->command->warn("Kelas untuk jurusan {$jurusan->nama_jurusan} tidak ditemukan!");
                continue;
            }

            $currentKelasIndex = 0;
            $maxPerKelas = $jurusan->kapasitas_per_kelas;
            $totalSiswaPerJurusan = $maxPerKelas * $jurusan->max_kelas;

            // Generate siswa untuk setiap jurusan
            for ($i = 1; $i <= $totalSiswaPerJurusan; $i++) {
                $selectedKelas = $kelas[$currentKelasIndex];

                // Jika kelas sudah penuh, pindah ke kelas berikutnya
                if ($selectedKelas->kapasitas_saat_ini >= $maxPerKelas) {
                    $currentKelasIndex++;
                    if ($currentKelasIndex >= $kelas->count()) {
                        $this->command->warn("Semua kelas untuk jurusan {$jurusan->nama_jurusan} sudah penuh!");
                        break;
                    }
                    $selectedKelas = $kelas[$currentKelasIndex];
                }

                // Generate NISN unik untuk setiap jurusan
                $nisn = '2024' . str_pad($jurusan->id, 2, '0', STR_PAD_LEFT) . str_pad($i, 3, '0', STR_PAD_LEFT);

                $datePart = now()->format('dmy'); // Format: tanggal, bulan, tahun (dua digit)
                $idPart = str_pad($i, 3, '0', STR_PAD_LEFT); // Format: 3 digit urutan siswa
                $daftar_id = $datePart . $idPart;

                $pendaftaran = Pendaftaran::create([
                    'daftar_id' => $daftar_id,
                    'NISN' => $nisn,
                    'nama' => 'Siswa ' . $jurusan->kode_jurusan . ' ' . chr(65 + ($i % 26)) . $i,
                    'alamat' => 'Alamat Siswa ' . $jurusan->kode_jurusan . ' ' . $i,
                    'tgl_lahir' => '2006-03-16',
                    'tmp_lahir' => 'Jakarta',
                    'jenis_kelamin' => $i % 2 == 0 ? 'L' : 'P',
                    'agama' => 'Islam',
                    'asal_sekolah' => 'SMP ' . ($i % 5 + 1),
                    'nama_ortu' => 'Orang Tua ' . $jurusan->kode_jurusan . ' ' . $i,
                    'pekerjaan_ortu' => $i % 2 == 0 ? 'Wiraswasta' : 'PNS',
                    'no_telp_ortu' => '08559930' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'tahun_ajaran_id' => $tahunAjaran->id,
                    'jurusan_id' => $jurusan->id,
                    'kelas_id' => $selectedKelas->id,
                    'status_dokumen' => true,
                    'nilai_semester_1' => rand(75, 100),
                    'nilai_semester_2' => rand(75, 100),
                    'nilai_semester_3' => rand(75, 100),
                    'nilai_semester_4' => rand(75, 100),
                    'nilai_semester_5' => rand(75, 100),
                    'rata_rata_nilai' => rand(75, 90),
                    'status_seleksi' => 'Lulus'
                ]);

                // Update kapasitas kelas
                $selectedKelas->increment('kapasitas_saat_ini');

                $noBayar = 'BYR' . date('Ymd') . str_pad($pendaftaran->id, 4, '0', STR_PAD_LEFT);

                // Generate administrasi
                Administrasi::create([
                    'pendaftaran_id' => $pendaftaran->id,
                    'tahun_ajaran_id' => $tahunAjaran->id,
                    'no_bayar' => $noBayar,
                    'tanggal_bayar' => now(),
                    'biaya_pendaftaran' => 100000,
                    'biaya_ppdb' => 5000000,
                    'biaya_mpls' => 250000,
                    'biaya_awal_tahun' => 1500000,
                    'total_bayar' => 6850000,
                    'status_pembayaran' => 'Lunas',
                    'is_pendaftaran_lunas' => true,
                    'is_ppdb_lunas' => true,
                    'is_mpls_lunas' => true,
                    'is_awal_tahun_lunas' => true,
                    'tanggal_bayar_pendaftaran' => now(),
                    'tanggal_bayar_ppdb' => now(),
                    'tanggal_bayar_mpls' => now(),
                    'tanggal_bayar_awal_tahun' => now(),
                ]);
            }

            $this->command->info("Data calon siswa untuk jurusan {$jurusan->nama_jurusan} berhasil dibuat!");
        }

        $this->command->info('Semua data calon siswa berhasil dibuat dan didistribusikan ke kelas!');
    }
}
