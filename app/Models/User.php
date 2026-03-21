<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Cashier\Billable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;


use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "User",
    title: "User Model",
    description: "User model",
    properties: [
        new OA\Property(property: "id", type: "integer", readOnly: true, example: 1),
        new OA\Property(property: "name", type: "string", example: "John Doe"),
        new OA\Property(property: "email", type: "string", format: "email", example: "john@example.com"),
        new OA\Property(property: "is_admin", type: "boolean", example: false),
        new OA\Property(property: "status", type: "string", example: "active"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", readOnly: true),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", readOnly: true)
    ]
)]
class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Billable, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $with = ['image'];
    
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, "imageable");
    }

    public function reservations()
    {

        return $this->hasMany(reservation::class);
    }


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }
}
