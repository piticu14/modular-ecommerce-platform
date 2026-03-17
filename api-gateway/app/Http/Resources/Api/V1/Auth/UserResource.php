<?php

namespace App\Http\Resources\Api\V1\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * @return array{id: int, name: string, email: string, created_at: string, updated_at: string}
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) ($this->resource['id'] ?? 1),
            'name' => (string) ($this->resource['name'] ?? 'John Doe'),
            'email' => (string) ($this->resource['email'] ?? 'user@example.com'),
            'created_at' => (string) ($this->resource['created_at'] ?? now()->toIso8601String()),
            'updated_at' => (string) ($this->resource['updated_at'] ?? now()->toIso8601String()),
        ];
    }
}
