<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Ticket",
    title: "Ticket Model",
    description: "Ticket model",
    properties: [
        new OA\Property(property: "id", type: "integer", readOnly: true, example: 1),
        new OA\Property(property: "reservation_id", type: "integer", example: 1),
        new OA\Property(property: "user_id", type: "integer", example: 1),
        new OA\Property(property: "seat_id", type: "integer", example: 1),
        new OA\Property(property: "qr_code_path", type: "string", example: "tickets/qr123.png"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", readOnly: true),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", readOnly: true)
    ]
)]
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
        return $this->belongsTo(Seat::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
