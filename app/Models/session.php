<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class session extends Model
{
    /** @use HasFactory<\Database\Factories\SessionFactory> */
    use HasFactory;

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function film()
    {
        return $this->belongsTo(Film::class);
    }
}
