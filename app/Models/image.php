<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class image extends Model
{
    public function imagable()
    {
        return $this->morphTo();
    }
}
