<?php
namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;

class Panitia extends Model implements AuthenticatableContract
{
    use HasFactory, Authenticatable;

    protected $table = 'panitias';

    protected $fillable = [
        'nama',
        'jabatan',
        'unit',
        'alamat',
        'no_hp',
        'email',
        'foto',
        'user_id',
        'username',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Implementasikan method yang dibutuhkan oleh AuthenticatableContract
    public function getAuthIdentifierName()
    {
        return 'id'; // Nama kolom untuk identifier (ID) di tabel Panitia
    }

    public function getAuthIdentifier()
    {
        return $this->getKey(); // Biasanya menggunakan primary key
    }

    public function getAuthPassword()
    {
        return $this->password; // Password yang digunakan untuk autentikasi
    }

    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }
}
