<?php

namespace App\Services;

use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;

class TahunAjaranService
{
    public function store(array $data)
    {
        DB::beginTransaction();
        try {
            // Format tahun_ajaran dari tahun_mulai dan tahun_selesai
            $data['tahun_ajaran'] = $data['tahun_mulai'] . '/' . $data['tahun_selesai'];

            // Nonaktifkan tahun ajaran lain jika yang ini diaktifkan
            if (isset($data['is_active']) && $data['is_active']) {
                TahunAjaran::where('is_active', true)->update(['is_active' => false]);
            }

            $tahunAjaran = TahunAjaran::create($data);

            DB::commit();
            return $tahunAjaran;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function update(TahunAjaran $tahunAjaran, array $data)
    {
        DB::beginTransaction();
        try {
            // Format tahun_ajaran dari tahun_mulai dan tahun_selesai
            $data['tahun_ajaran'] = $data['tahun_mulai'] . '/' . $data['tahun_selesai'];

            // Nonaktifkan tahun ajaran lain jika yang ini diaktifkan
            if (isset($data['is_active']) && $data['is_active']) {
                TahunAjaran::where('id', '!=', $tahunAjaran->id)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
            }

            $tahunAjaran->update($data);

            DB::commit();
            return $tahunAjaran;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
