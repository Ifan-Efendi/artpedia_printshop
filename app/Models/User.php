<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'telepon',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Role constants
     */
    const ROLE_PELANGGAN = 'pelanggan';
    const ROLE_KASIR = 'kasir';
    const ROLE_OPERATOR = 'operator_produksi';

    /**
     * Get user's profile
     */
    public function profile()
    {
        return $this->hasOne(Profile::class, 'users_id');
    }

    /**
     * Get user's orders (for pelanggan)
     */
    public function pesanan()
    {
        return $this->hasMany(Pesanan::class);
    }

    /**
     * Get orders confirmed by this user (for kasir)
     */
    public function pesananDikonfirmasi()
    {
        return $this->hasMany(Pesanan::class, 'dikonfirmasi_oleh');
    }

    /**
     * Get orders processed by this user (for operator)
     */
    public function pesananDiproses()
    {
        return $this->hasMany(Pesanan::class, 'diproses_oleh');
    }

    /**
     * Check if user is pelanggan
     */
    public function isPelanggan()
    {
        return $this->role === self::ROLE_PELANGGAN;
    }

    /**
     * Check if user is kasir
     */
    public function isKasir()
    {
        return $this->role === self::ROLE_KASIR;
    }

    /**
     * Check if user is operator produksi
     */
    public function isOperator()
    {
        return $this->role === self::ROLE_OPERATOR;
    }

    /**
     * Get role label for display
     */
    public function getRoleLabelAttribute()
    {
        return match ($this->role) {
            'pelanggan' => 'Pelanggan',
            'kasir' => 'Kasir',
            'operator_produksi' => 'Operator Produksi',
            default => $this->role,
        };
    }

}
