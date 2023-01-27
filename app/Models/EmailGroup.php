<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailGroup extends Model
{

    protected $fillable = [
      'name',
      'emails',
    ];

    public function user(){
        return $this->belongsTo(user::class);
    }
}
