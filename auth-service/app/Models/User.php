<?php

    namespace App\Models;

    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Tymon\JWTAuth\Contracts\JWTSubject;

    class User extends Authenticatable implements JWTSubject
    {

        protected $fillable = [
            'name',
            'email',
            'password',
        ];

        protected $hidden = [
            'password',
        ];

        protected function casts(): array
        {
            return [
                'password' => 'hashed',
            ];
        }

        /**
         * Return the identifier stored in the JWT token.
         */
        public function getJWTIdentifier()
        {
            return $this->getKey();
        }

        /**
         * Custom claims added to JWT.
         */
        public function getJWTCustomClaims(): array
        {
            return [];
        }
    }
