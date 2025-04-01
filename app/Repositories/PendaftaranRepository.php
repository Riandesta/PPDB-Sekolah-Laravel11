<?php

namespace App\Repositories;

use App\Models\Pendaftaran;

Class PendaftaranRepository
{
    public function getAll()
    {
        return Pendaftaran::all();
    }

    public function create(array $data)
    {
        return Pendaftaran::create($data);
    }

    public function update(Pendaftaran $Pendaftaran, array $data)
    {
        return $Pendaftaran->update($data);
    }

    public function delete(Pendaftaran $Pendaftaran)
    {
        return $Pendaftaran->delete();
    }
}
