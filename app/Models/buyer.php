<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class buyer extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'email', 'phone', 'uuid'];

    public function shops()
    {
        return $this->belongsToMany(shop::class)->withPivot('email', 'phone', 'email_verification_token', 'email_verified_at')->withTimestamps();
    }
}
