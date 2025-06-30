<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gateway extends Model
{
    protected $fillable = [
        'shop_id', 'gateway', 'merchant_id', 'api_key', 'secret', 'callback_url', 'sandbox'
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
