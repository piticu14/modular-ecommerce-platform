<?php

namespace App\Http\Resources\Api\V1\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TokenResource extends JsonResource
{
    /**
     * @return array{access_token: string, token_type: string, expires_in: int}
     */
    public function toArray(Request $request): array
    {
        return [
            'access_token' => (string) ($this->resource['access_token'] ?? 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...'),
            'token_type' => (string) ($this->resource['token_type'] ?? 'bearer'),
            'expires_in' => (int) ($this->resource['expires_in'] ?? 3600),
        ];
    }
}
