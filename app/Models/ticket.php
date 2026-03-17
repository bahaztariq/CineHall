<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ticket extends Model
{
    /** @use HasFactory<\Database\Factories\TicketFactory> */
    protected $fillable = [
        'reservation_id',
        'user_id',
        'seat_id',
    ];

    public function reservation()
    {
        return $this->belongsTo(reservation::class);
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }
}
