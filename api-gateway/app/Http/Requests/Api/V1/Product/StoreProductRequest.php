<?php

namespace App\Http\Requests\Api\V1\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
             * The product's name.
             *
             * @example My Product
             */
            'name' => ['required', 'string', 'max:255'],
            /**
             * The product's price as integer.
             *
             * @example 29999
             */
            'price' => ['required', 'integer', 'min:0'],
            /**
             * The 3-letter currency code.
             *
             * @example CZK
             */
            'currency' => ['required', 'string', 'size:3'],
            /**
             * The initial stock count.
             *
             * @example 100
             */
            'stock_on_hand' => ['nullable', 'integer', 'min:0'],

        ];
    }
}
