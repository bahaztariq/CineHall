<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ticket extends Model
{
    /** @use HasFactory<\Database\Factories\TicketFactory> */
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'user_id',
        'seat_id',
        'qr_code_path',
    ];


    public function reservation(){
        return $this->belongsTo(reservation::class);
    }


    public function seat(){
        return $this->belongsTo(seat::class);
    }
    public function user(){
        return $this->belongsTo(user::class);
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
