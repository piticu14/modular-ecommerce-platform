<?php

namespace App\Http\Requests\Api\V1\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
             * List of items to order.
             */
            'items' => ['required', 'array', 'min:1'],
            /**
             * The product UUID.
             *
             * @example 8b3f99b6-0000-0000-0000-000000000000
             */
            'items.*.product_uuid' => ['required', 'string', 'uuid'],
            /**
             * The quantity to order.
             *
             * @example 2
             */
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ];
    }
}
