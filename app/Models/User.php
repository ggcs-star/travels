<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Role Checks
    |--------------------------------------------------------------------------
    */

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }


    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }


    public function isUser(): bool
    {
        return $this->role === 'user';
    }


    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->status === true;
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    public function blogs()
{
    return $this->hasMany(
        Blog::class,
        'author_id'
    );
}
}
