<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable =['buyer_id', 'shop_id', 'status','total'];
    public function buyer(){
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
