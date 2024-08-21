<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable =[
        'total',
        'status',
        'productnumber',
        'amount'
    ];
    public function buyer(){
        return $this->belongsTo(User::class, 'user_id');
    }
    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('order_id');
    }
}
