<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const STATUS_PENDING   = 'pending';
    const STATUS_RESERVED  = 'reserved';
    const STATUS_PAID      = 'paid';
    const STATUS_SHIPPED   = 'shipped';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    protected $casts = [
        'paid_at' => 'datetime',
        'reserved_at' => 'datetime',
        'reservation_expires_at' => 'datetime',
    ];

    protected $fillable = [
        'buyer_id',
        'shop_id',
        'status',
        'total',
        'paid_at',
        'reserved_at',
        'reservation_expires_at',
        'tracking_code',
        'receiver_name',
        'receiver_phone',
        'receiver_province',
        'receiver_city',
        'receiver_postal_code',
        'receiver_address',
    ];

    public function buyer()
    {
        return $this->belongsTo(Buyer::class, 'buyer_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity', 'price');
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class); // یک سفارش یک پرداخت نهایی دارد
    }
}
