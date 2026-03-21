<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Favourite",
    title: "Favourite Model",
    description: "User favourite film model",
    properties: [
        new OA\Property(property: "id", type: "integer", readOnly: true, example: 1),
        new OA\Property(property: "user_id", type: "integer", example: 1),
        new OA\Property(property: "film_id", type: "integer", example: 1),
        new OA\Property(property: "created_at", type: "string", format: "date-time", readOnly: true),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", readOnly: true)
    ]
)]
class favourite extends Model
{
    /** @use HasFactory<\Database\Factories\FavouriteFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'film_id'];
}
