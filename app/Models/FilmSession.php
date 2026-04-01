<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FilmSession extends Model
{
    use HasFactory;

    // Mass assignment protection
    protected $fillable = [
        'film_id',
        'room_id',
        'language',
        'start_time',
        'end_time',
        'price'
    ];

    // Ensure dates are treated as Carbon instances
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function film(): BelongsTo
    {
        return $this->belongsTo(Film::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}