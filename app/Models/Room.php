<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\session;

class Room extends Model
{
    /** @use HasFactory<\Database\Factories\RoomFactory> */
    use HasFactory;
    
    protected $fillable = ['name','type', 'capacity'];

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(session::class);
    }
}
