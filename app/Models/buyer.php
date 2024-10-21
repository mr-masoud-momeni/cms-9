<?php

namespace App\Models;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;

class buyer extends Model implements AuthenticatableContract
{
    use Authenticatable;
    use HasFactory;
    protected $fillable = ['name', 'email', 'phone', 'uuid', 'password'];

    public function shops()
    {
        return $this->belongsToMany(shop::class)->withPivot('email', 'phone', 'email_verification_token', 'email_verified_at')->withTimestamps();
    }
}
