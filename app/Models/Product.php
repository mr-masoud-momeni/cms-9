<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use Sluggable;

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    protected $fillable = [
        'user_id',
        'shop_id',
        'title',
        'body',
        'images',
        'slug',
        'viewCount',
        'comentCount',
        'link',
        'product-body',
        'price-type',
        'price',
        'unit',
        'stock',
    ];

    protected $casts = [
        'images' => 'array',
        'stock' => 'integer',
    ];

    public function getStockLabelAttribute(): string
    {
        return $this->stock . ' ' . $this->unit;
    }

    public function getAvailableStockAttribute(): int
    {
        if ($this->stock <= 0) {
            return 0;
        }

        $now = now();

        $reserved = $this->orders()
            ->where('orders.status', Order::STATUS_RESERVED)
            ->where(function ($query) use ($now) {
                $query->where('orders.reservation_expires_at', '>', $now)
                    ->orWhereHas('payment', function ($paymentQuery) {
                        $paymentQuery->where('status', 'waiting_confirmation');
                    });
            })
            ->sum('order_product.quantity');

        return max(0, (int) $this->stock - (int) $reserved);
    }

    public function availableStockLabel(): string
    {
        return $this->available_stock . ' ' . $this->unit;
    }

    public function getRoutekeyName()
    {
        return 'slug';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function categories()
    {
        return $this->morphToMany(Category::class, 'categorizable');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class)->withPivot('quantity', 'price');
    }
}
