<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Film",
    title: "Film Model",
    description: "Film model",
    properties: [
        new OA\Property(property: "id", type: "integer", readOnly: true, example: 1),
        new OA\Property(property: "title", type: "string", example: "Inception"),
        new OA\Property(property: "description", type: "string", example: "A thief who steals corporate secrets..."),
        new OA\Property(property: "duration", type: "integer", example: 148),
        new OA\Property(property: "rate", type: "string", example: "A+"),
        new OA\Property(property: "trailer", type: "string", example: "https://youtube.com/watch?v=..."),
        new OA\Property(property: "created_at", type: "string", format: "date-time", readOnly: true),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", readOnly: true)
    ]
)]
class Film extends Model
{
    /** @use HasFactory<\Database\Factories\FilmFactory> */
    use HasFactory;

    protected $fillable = ['title', 'description', 'duration', 'rate', 'trailer'];

    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }

    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, "imageable");
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(genre::class, 'film_genres');
    }
}
