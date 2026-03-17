<?php

namespace App\Http\Requests\Api\V1\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            /**
             * Comma-separated list of product IDs (UUIDs) to filter by.
             * @example 9a2e88a5-0000-0000-0000-000000000000
             */
            'ids' => ['nullable', 'string'],
        ];
    }
}
