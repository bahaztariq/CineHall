<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    protected $fillable = [
        'room_id',
        'seat_number',
        'type',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function tickets()
    {
        return $this->hasMany(ticket::class);
    }

    public function reservations()
    {
        return $this->hasMany(reservation::class);
    }
}
