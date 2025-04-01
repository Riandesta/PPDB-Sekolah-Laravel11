<?php
namespace Database\Seeders;

use App\Models\User;
use App\Models\Panitia;
use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserAndRoleSeeder extends Seeder
{
    public function run()
    {

User::create([
    'nama' => 'Administrator',
    'username' => 'admin',
    'role' => 'admin',
    'password' => bcrypt('admin123'),
]);

User::create([
    'nama' => 'Panitia PPDB',
    'username' => 'panitia',
    'role' => 'panitia',
    'password' => bcrypt('panitia123'),
]);
User::create([
    'nama' => 'User',
    'username' => 'user',
    'role' => 'user',
    'password' => bcrypt('user123'),
]);

}
}
