<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            TahunAjaranSeeder::class,
            UserAndRoleSeeder::class,
            JurusanSeeder::class,
            KelasSeeder::class,
            KuotaPPDBSeeder::class,
            PendaftaranSeeder::class,
            AdministrasiSeeder::class,
        ]);
    }
}

