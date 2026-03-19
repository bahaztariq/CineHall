<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class reservation extends Model
{
    /** @use HasFactory<\Database\Factories\ReservationFactory> */
    use HasFactory;

    protected $fillable = [
       'user_id',
       'session_id',
       'seat_id',
       'status',
       'reserved_at',
       'paid_at',
    ];


    public function user(){

       return $this->belongsTo(User::class);
    }

    public function seat(){

       return $this->belongsTo(Seat::class);
    }

    public function session(){

       return $this->belongsTo(session::class, 'session_id');
    }

    public function tickets(){

       return $this->hasMany(ticket::class);
    }

    public function status(){

       return $this->status;
    }

    public function ispaid(){
      
      return $this->status === 'accepted';
    }

    /**
     * Confirm a payment for the reservation.
     * Updates status to 'accepted', sets 'paid_at', and creates a ticket.
     */
    public function confirmPayment()
    {
        if ($this->status === 'accepted') {
            return;
        }

        $this->update([
            'status'  => 'accepted',
            'paid_at' => now(),
        ]);

        // Auto-create a ticket for the confirmed reservation
        return ticket::firstOrCreate(
            ['reservation_id' => $this->id],
            [
                'user_id' => $this->user_id,
                'seat_id' => $this->seat_id,
            ]
        );
    }
}


