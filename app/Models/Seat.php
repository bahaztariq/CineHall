<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Seat",
    title: "Seat Model",
    description: "Seat model",
    properties: [
        new OA\Property(property: "id", type: "integer", readOnly: true, example: 1),
        new OA\Property(property: "room_id", type: "integer", example: 1),
        new OA\Property(property: "seat_number", type: "string", example: "A1"),
        new OA\Property(property: "type", type: "string", example: "VIP"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", readOnly: true),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", readOnly: true)
    ]
)]
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
