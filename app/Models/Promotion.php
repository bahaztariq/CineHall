<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Promotion",
    title: "Promotion Model",
    description: "Promotion/Discount model",
    properties: [
        new OA\Property(property: "id", type: "integer", readOnly: true, example: 1),
        new OA\Property(property: "name", type: "string", example: "Summer Sale"),
        new OA\Property(property: "discount_percentage", type: "integer", example: 20),
        new OA\Property(property: "start_date", type: "string", format: "date", example: "2024-06-01"),
        new OA\Property(property: "end_date", type: "string", format: "date", example: "2024-08-31"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", readOnly: true),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", readOnly: true)
    ]
)]
class Promotion extends Model
{
    /** @use HasFactory<\Database\Factories\PromotionFactory> */
    use HasFactory;

    protected $fillable = ['name', 'discount_percentage', 'start_date', 'end_date'];

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }
}
