<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopBaleConnection extends Model
{
    protected $fillable = [
        'shop_id',
        'user_id',
        'bale_user_id',
        'bale_chat_id',
        'active',
        'connected_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'connected_at' => 'datetime',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}