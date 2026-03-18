<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class session extends Model
{
    /** @use HasFactory<\Database\Factories\SessionFactory> */
    use HasFactory;

    protected $table = 'film_sessions' ;
    
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
