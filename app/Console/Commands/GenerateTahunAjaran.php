<?php

namespace App\Console\Commands;

use App\Models\TahunAjaran;
use Illuminate\Console\Command;

class GenerateTahunAjaran extends Command
{
    protected $signature = 'tahun-ajaran:generate';
    protected $description = 'Generate tahun ajaran baru dan nonaktifkan tahun ajaran lama';

    public function handle()
    {
        try {
            $currentYear = date('Y');
            $nextYear = $currentYear + 1;

            // Check if tahun ajaran already exists
            $existingTahunAjaran = TahunAjaran::where('tahun_ajaran', "$currentYear/$nextYear")->first();

            if ($existingTahunAjaran) {
                $this->error("Tahun ajaran $currentYear/$nextYear sudah ada");
                return 1;
            }

            // Log status awal
            $this->info("Memulai pembuatan tahun ajaran $currentYear/$nextYear");

            // Nonaktifkan semua tahun ajaran yang aktif
            $deactivated = TahunAjaran::where('is_active', true)
                ->update(['is_active' => false]);
            $this->info("Menonaktifkan tahun ajaran aktif");

            // Buat tahun ajaran baru
            $tahunAjaran = TahunAjaran::create([
                'tahun_ajaran' => "$currentYear/$nextYear",
                'tahun_mulai' => $currentYear,
                'tahun_selesai' => $nextYear,
                'is_active' => true,
                'tanggal_mulai' => "$currentYear-07-01",
                'tanggal_selesai' => "$nextYear-06-30",
            ]);

            if ($tahunAjaran) {
                $this->info("Tahun ajaran $currentYear/$nextYear berhasil dibuat dengan ID: " . $tahunAjaran->id);

                // Generate related data (KuotaPPDB, Kelas, etc.)
                $this->call('db:seed', [
                    '--class' => 'KuotaPPDBSeeder'
                ]);

                $this->call('db:seed', [
                    '--class' => 'KelasSeeder'
                ]);

                return 0;
            }

            $this->error("Gagal membuat tahun ajaran");
            return 1;

        } catch (\Exception $e) {
            $this->error("Terjadi kesalahan: " . $e->getMessage());
            return 1;
        }
    }
}
