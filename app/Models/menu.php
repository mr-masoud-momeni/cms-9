<?php

namespace App\Models;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class menu extends Model
{
    protected $fillable = [
        'title',
        'content',
    ];
    public function user(){
        return $this->belongsTo(user::class);
    }
}
