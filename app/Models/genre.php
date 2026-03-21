<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Genre",
    title: "Genre Model",
    description: "Genre model",
    properties: [
        new OA\Property(property: "id", type: "integer", readOnly: true, example: 1),
        new OA\Property(property: "name", type: "string", example: "Action")
    ]
)]
class genre extends Model
{
    /** @use HasFactory<\Database\Factories\GenreFactory> */
    use HasFactory;

    protected $table = 'genres';

    protected $fillable = ['name'];

    public function films(): BelongsToMany
    {
        return $this->belongsToMany(Film::class, 'film_genres');
    }
}
