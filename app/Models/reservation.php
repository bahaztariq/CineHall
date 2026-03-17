<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}


