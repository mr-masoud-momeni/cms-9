<?php

namespace App\Models;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Laratrust\Traits\LaratrustUserTrait;
use Illuminate\Support\Str;

class Buyer extends Model implements AuthenticatableContract
{
    use Authenticatable;
    use LaratrustUserTrait;
    use HasFactory;
    protected $fillable = ['name', 'email', 'phone', 'uuid', 'password'];
    protected $guard = 'buyer';
    /**
     * تعیین نوع مدل برای جدول واسط role_user
     */
    public function getMorphClass()
    {
        return static::class; // یعنی App\Models\Buyer
    }
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
        return $this->belongsToMany(shop::class)->withPivot('email', 'phone', 'email_verification_token', 'email_verified_at')->withTimestamps();
    }
    // سفارش‌هایی که خریدار ثبت کرده
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

}
