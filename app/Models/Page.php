<?php

namespace App\Models;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Dotlogics\Grapesjs\App\Traits\EditableTrait;
use Dotlogics\Grapesjs\App\Contracts\Editable;


class Page extends Model implements Editable
{
    use EditableTrait;
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
                'source' => 'url'
            ]
        ];
    }
    protected $fillable = [
        'title',
        'slug',
        'url',
        'status',
        'gjs_data',
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
