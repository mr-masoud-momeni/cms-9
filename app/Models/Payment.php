<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'shop_id',
        'gateway_id',
        'order_id',
        'ref_id',
        'sale_reference_id',
        'sale_order_id',
        'amount',
        'status'
    ];

    public function gateway()
    {
        return $this->belongsTo(Gateway::class);
    }

    public function store()
    {
        return $this->belongsTo(Shop::class);
    }
}
