<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class StoreOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_uuid' => ['required', 'string', 'min:1'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ];
    }

    #[Override]
    public function messages(): array
    {
        return [
            'items.required' => 'Pole items je povinné.',
            'items.array' => 'Pole items musí být array.',
            'items.min' => 'Objednávka musí obsahovat alespoň jednu položku.',
        ];
    }
}
