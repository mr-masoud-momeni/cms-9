<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laratrust\Traits\LaratrustUserTrait;
use Illuminate\Support\Str;

class Buyer extends Authenticatable
{
    use LaratrustUserTrait, HasFactory;

    protected $guard = 'buyer';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'uuid',
        'password',
    ];

    protected static function booted()
    {
        static::creating(function ($buyer) {
            if (empty($buyer->uuid)) {
                $buyer->uuid = (string) Str::uuid();
            }
        });
    }

    public function shops()
    {
        return $this->belongsToMany(Shop::class)
            ->withPivot('email', 'phone', 'email_verification_token', 'email_verified_at')
            ->withTimestamps();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
