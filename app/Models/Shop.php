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
}
