<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Session",
    title: "Session Model",
    description: "Session (Film Showing) model",
    properties: [
        new OA\Property(property: "id", type: "integer", readOnly: true, example: 1),
        new OA\Property(property: "film_id", type: "integer", example: 1),
        new OA\Property(property: "room_id", type: "integer", example: 1),
        new OA\Property(property: "language", type: "string", example: "English"),
        new OA\Property(property: "start_time", type: "string", format: "date-time", example: "2024-03-22T18:00:00Z"),
        new OA\Property(property: "end_time", type: "string", format: "date-time", example: "2024-03-22T20:00:00Z"),
        new OA\Property(property: "price", type: "number", format: "float", example: 12.50),
        new OA\Property(property: "created_at", type: "string", format: "date-time", readOnly: true),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", readOnly: true)
    ]
)]
class session extends Model
{
    /** @use HasFactory<\Database\Factories\SessionFactory> */
    use HasFactory;

    protected $table = 'film_sessions' ;

    protected $fillable = ['film_id','room_id','language','start_time','end_time','price'];

    public function film(): BelongsTo
    {
        return $this->belongsTo(Film::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

}
