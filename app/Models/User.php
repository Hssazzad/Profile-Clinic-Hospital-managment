<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'name',
        'email',
        'phone',              // ✅ add
        'password',
        'google_id',
        'avatar',
        'last_login_at',      // ✅ optional
        'last_login_ip',      // ✅ optional
        'phone_verified_at',  // ✅ optional
    ];

    /**
     * Hidden for arrays/JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',  // ✅
            'last_login_at'     => 'datetime',  // ✅
            'password'          => 'hashed',
        ];
    }
}
