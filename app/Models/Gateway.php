<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gateway extends Model
{
    protected $fillable = [
        'shop_id',
        'title',
        'terminal_id',
        'username',
        'password',
        'wsdl_url',
        'gateway_url',
        'active',
    ];

    public function store()
    {
        return $this->belongsTo(Shop::class);
    }
}
