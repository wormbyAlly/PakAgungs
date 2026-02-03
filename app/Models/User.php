<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;




    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    protected $appends = [
        'last_login_wib',
    ];


    public function product()
    {
        return $this->hasMany(Product::class);
    }




    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }


    //  MUTATOR
    public function setPasswordAttribute($value)
    {
        if ($value && Hash::needsRehash($value)) {
            $this->attributes['password'] = Hash::make($value);
            return;
        }

        $this->attributes['password'] = $value;
    }

    public function getLastLoginWibAttribute(): ?string
    {
        if (! $this->last_login_at) {
            return null;
        }

        return $this->last_login_at
            ->timezone('Asia/Jakarta')
            ->format('d M Y H:i');
    }
}
