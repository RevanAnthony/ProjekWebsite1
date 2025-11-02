<?php 

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // optional

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'pengguna';
    protected $primaryKey = 'id_pengguna';
    public $timestamps = true;

    protected $fillable = [
        'nama', 'email', 'password', 'nomor_telepon', 'google_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
