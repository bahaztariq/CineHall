<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\session;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Room",
    title: "Room Model",
    description: "Room model",
    properties: [
        new OA\Property(property: "id", type: "integer", readOnly: true, example: 1),
        new OA\Property(property: "name", type: "string", example: "Main Hall"),
        new OA\Property(property: "type", type: "string", example: "VIP"),
        new OA\Property(property: "capacity", type: "integer", example: 100),
        new OA\Property(property: "created_at", type: "string", format: "date-time", readOnly: true),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", readOnly: true)
    ]
)]
class Room extends Model
{
    /** @use HasFactory<\Database\Factories\RoomFactory> */
    use HasFactory;
    
    protected $fillable = ['name','type', 'capacity'];

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(session::class);
    }
}
