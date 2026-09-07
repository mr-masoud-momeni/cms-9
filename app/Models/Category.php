<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use Sluggable;

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'slug'
            ]
        ];
    }

    protected $fillable = [
        'shop_id',
        'user_id',
        'name',
        'slug',
        'parent_id',
        'type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function article()
    {
        return $this->morphedByMany(Article::class, 'categorizable');
    }

    public function product()
    {
        return $this->morphedByMany(Product::class, 'categorizable');
    }

    public function subcategories()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
