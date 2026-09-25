<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Helpers\ShopHelper;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Shop extends Model
{
    use HasFactory;
    use Sluggable;

    protected static function booted(): void
    {
        static::creating(function (Shop $shop) {
            $shop->uuid = (string) \Illuminate\Support\Str::uuid();
        });

        static::updating(function (Shop $shop) {
            if ($shop->isDirty('domain')) {
                ShopHelper::forgetShopCache($shop->getOriginal('domain'));
            }
        });

        static::saved(function (Shop $shop) {
            ShopHelper::forgetShopCache($shop->domain);
        });

        static::deleted(function (Shop $shop) {
            ShopHelper::forgetShopCache($shop->domain);
        });
    }

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
        'logo',
        'description',
        'shipping_cost',
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

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function buyers()
    {
        return $this->belongsToMany(buyer::class)->withPivot('email', 'phone', 'email_verification_token', 'email_verified_at')->withTimestamps();
    }

    public static function current()
    {
        return ShopHelper::getShop();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function gateways()
    {
        return $this->hasMany(Gateway::class);
    }

    public function baleConnections()
    {
        return $this->hasMany(ShopBaleConnection::class);
    }

    public function baleConnectionTokens()
    {
        return $this->hasMany(ShopBaleConnectionToken::class);
    }

    public function bankAccount()
    {
        return $this->hasOne(ShopBankAccount::class);
    }
}
