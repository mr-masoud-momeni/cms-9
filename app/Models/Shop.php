<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Shop extends Model
{
    use HasFactory;
    use Sluggable;

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
    protected $fillable =[
        'user_id',
        'name',
        'domain',
        'slug',
    ];

    public function getRoutekeyName(){
        return 'slug';
    }
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function product(){
        return $this->hasMany(product::class);
    }
    public function buyers()
    {
        return $this->belongsToMany(buyer::class)->withPivot('email', 'phone', 'email_verification_token', 'email_verified_at')->withTimestamps();
    }
    public static function current()
    {
        $host = request()->getHost(); // مثلاً: shop.example.com
        return self::where('domain', $host)->first();
    }
    // سفارش‌هایی که در این فروشگاه ثبت شده
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
