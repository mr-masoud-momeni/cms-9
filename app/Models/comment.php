<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class comment extends Model
{
    public function commentable()
    {
        return $this->morphTo();
    }
}
