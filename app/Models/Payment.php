<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Payment",
    title: "Payment Model",
    description: "Payment transaction model",
    properties: [
        new OA\Property(property: "id", type: "integer", readOnly: true, example: 1),
        new OA\Property(property: "reservation_id", type: "integer", example: 1),
        new OA\Property(property: "amount", type: "number", format: "float", example: 25.00),
        new OA\Property(property: "status", type: "string", enum: ["pending", "completed", "failed"], example: "completed"),
        new OA\Property(property: "payment_method", type: "string", example: "stripe"),
        new OA\Property(property: "transaction_id", type: "string", example: "pi_123456789"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", readOnly: true),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", readOnly: true)
    ]
)]
class Payment extends Model
{

    protected $fillable = [
        'reservation_id',
        'amount',
        'status',
        'payment_method',
        'transaction_id',
    ];


}
