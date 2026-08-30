<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopBankAccount extends Model
{
    protected $fillable = [
        'shop_id',
        'card_number',
        'sheba',
        'account_holder',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
