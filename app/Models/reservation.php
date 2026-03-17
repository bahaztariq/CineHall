<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class reservation extends Model
{


    protected $fillable = [
        'reservation_id',
        'user_id',
        'seat_number'
    ];

    

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}


