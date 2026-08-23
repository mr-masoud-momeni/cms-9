<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentReceipt extends Model
{
    protected $fillable = [
        'payment_id',
        'image',
        'tracking_code',
        'description',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}