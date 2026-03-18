<?php

namespace App\Http\Resources\Api\V1\Common;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ErrorResource extends JsonResource
{
    /**
     * @return array{message: string}
     */
    public function toArray(Request $request): array
    {
        // Scramble (docs)
        if ($this->resource === null) {
            return [
                'message' => 'Error message',
            ];
        }

        /**
         * @var array{
         *     message: string,
         * } $data
         */
        $data = $this->resource;

        return $data;
    }
}
