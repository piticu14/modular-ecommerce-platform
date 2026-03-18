<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Override;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /**
     * @use HasFactory<UserFactory>
     */
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    #[Override]
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Return the identifier stored in the JWT token.
     */
    #[Override]
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Custom claims added to JWT.
     */
    #[Override]
    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
