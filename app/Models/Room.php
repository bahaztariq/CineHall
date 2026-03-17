<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    /** @use HasFactory<\Database\Factories\RoomFactory> */
    use HasFactory;

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }
}
