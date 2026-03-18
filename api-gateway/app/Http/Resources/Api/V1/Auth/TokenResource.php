<?php

namespace App\Http\Resources\Api\V1\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Override;

class TokenResource extends JsonResource
{
    /**
     * @return array{access_token: string, token_type: string, expires_in: int}
     */
    #[Override]
    public function toArray(Request $request): array
    {

        // Scramble (docs)
        if ($this->resource === null) {
            return [
                'access_token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...',
                'token_type' => 'bearer',
                'expires_in' => 3600,
            ];
        }

        /** @var array{
         *     access_token: string,
         *     token_type: string,
         *     expires_in: int
         * } $data
         */
        $data = $this->resource;

        return $data;
    }
}
