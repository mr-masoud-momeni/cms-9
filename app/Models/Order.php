<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable =[
        'total',
        'status'
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('amount');
    }
    public function scopesearch($id){
        dd($id);
        return $this->belongsToMany(Product::class)->wherePivot('product_id', $id);
    }
}
