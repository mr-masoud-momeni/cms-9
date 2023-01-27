<?php

namespace App;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use Sluggable;

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'url'
            ]
        ];
    }
    protected $fillable = [
        'title',
        'slug',
        'url',
        'status',
        'html',
        'styles',
        'css',
        'assets',
        'components',
    ];
    public function scopeSearch($query , $search , $staatus)
    {
        $query->where('title' , 'LIKE' , '%' . $search . '%' )
              ->where('status' , 'LIKE' , '%' . $staatus . '%' );
        return $query;
    }
    public function getRoutekeyName(){
        return 'slug';
    }
    public function user(){
        return $this->belongsTo(user::class);
    }
}
