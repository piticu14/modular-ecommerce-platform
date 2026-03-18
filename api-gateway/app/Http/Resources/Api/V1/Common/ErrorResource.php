<?php

namespace App\Http\Resources\Api\V1\Common;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Override;

class ErrorResource extends JsonResource
{
    /**
     * @return array{message: string}
     */
    #[Override]
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
