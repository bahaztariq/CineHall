<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Reservation",
    title: "Reservation Model",
    description: "Reservation model",
    properties: [
        new OA\Property(property: "id", type: "integer", readOnly: true, example: 1),
        new OA\Property(property: "user_id", type: "integer", example: 1),
        new OA\Property(property: "session_id", type: "integer", example: 1),
        new OA\Property(property: "seat_id", type: "integer", example: 1),
        new OA\Property(property: "status", type: "string", enum: ["pending", "accepted", "cancelled"], example: "pending"),
        new OA\Property(property: "reserved_at", type: "string", format: "date-time", example: "2024-03-21T15:23:57Z"),
        new OA\Property(property: "paid_at", type: "string", format: "date-time", nullable: true, example: null),
        new OA\Property(property: "created_at", type: "string", format: "date-time", readOnly: true),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", readOnly: true)
    ]
)]
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


