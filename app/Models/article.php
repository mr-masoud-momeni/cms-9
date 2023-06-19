<?php

namespace App\Models;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class article extends Model
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
        'title',
        'body',
        'images',
        'slug',
        'viewCount',
        'comentCount',
    ];
    protected $casts = ['images' => 'array'];

    public function getRoutekeyName(){
        return 'slug';
    }
    public function user(){
        return $this->belongsTo(user::class);
    }
//    public function category(){
//        return $this->belongsToMany(category::class);
//    }
    public function categories(){
        return $this->morphToMany(category::class , 'categorizable');
    }
    public function Permission(){
        return $this->belongsToMany(Permission::class);
    }
    /**
     * Get all of the post's comments.
     */
    public function comments()
    {
        return $this->morphMany(comment::class, 'commentable');
    }
}
