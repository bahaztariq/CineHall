<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Image",
    title: "Image Model",
    description: "Polymorphic Image model",
    properties: [
        new OA\Property(property: "id", type: "integer", readOnly: true, example: 1),
        new OA\Property(property: "path", type: "string", example: "films/inception.jpg"),
        new OA\Property(property: "imageable_id", type: "integer", example: 1),
        new OA\Property(property: "imageable_type", type: "string", example: "App\\Models\\Film")
    ]
)]
class Image extends Model
{
    

    public $timestamps = false;
    protected $fillable = ['path'];

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}