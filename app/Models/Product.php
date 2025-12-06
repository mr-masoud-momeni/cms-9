<?php

namespace App\Models;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class product extends Model
{
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
                'source' => 'title'
            ]
        ];
    }
    protected $fillable =[
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
    ];
    protected $casts = ['images' => 'array'];

    public function getRoutekeyName(){
        return 'slug';
    }
    public function user(){
        return $this->belongsTo(user::class);
    }
    public function shop(){
        return $this->belongsTo(shop::class);
    }
    public function categories()
    {
        return $this->morphToMany(category::class, 'categorizable');
    }
    public function orders()
    {
        return $this->belongsToMany(Order::class)->withPivot('quantity', 'price');
    }

}
