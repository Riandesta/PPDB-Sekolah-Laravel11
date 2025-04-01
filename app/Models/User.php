<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'nama',
        'username',
        'password',
        'role'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Helper method to check user role
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isPanitia()
    {
        return $this->role === 'panitia';
    }

    public function panitia()
    {
        return $this->hasMany(Panitia::class);
    }
}
