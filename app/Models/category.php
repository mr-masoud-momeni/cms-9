<?php

namespace App;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class category extends Model
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
                'source' => 'slug'
            ]
        ];
    }
    protected $fillable=[
        'name',
        'slug',
        'parent_id',
        'type',
    ];
    public function user(){
        return $this->belongsTo(user::class);
    }
//    public function article(){
//        return $this->belongsToMany(article::class);
//    }
    public function article(){
        return $this->morphedByMany(article::class , 'categorizable' );
    }
    public function subcategory(){
        return $this->hasMany('App\category', 'parent_id');
    }
}
