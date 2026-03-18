<?php

namespace App\Http\Resources\Api\V1\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Override;

class UserResource extends JsonResource
{
    /**
     * @return array{
     *     id: int,
     *     name: string,
     *     email: string,
     *     created_at: string,
     *     updated_at: string
     * }
     */
    #[Override]
    public function toArray(Request $request): array
    {
        // Scramble (docs)
        if ($this->resource === null) {
            return [
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'user@example.com',
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString(),
            ];
        }

        /**
         * @var array{
         *     id: int,
         *     name: string,
         *     email: string,
         *     created_at: string,
         *     updated_at: string
         * } $data
         */
        $data = $this->resource;

        return $data;
    }
}
