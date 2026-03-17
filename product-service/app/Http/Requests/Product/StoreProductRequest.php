<?php

    namespace App\Http\Requests\Product;

    use Illuminate\Foundation\Http\FormRequest;

    class StoreProductRequest extends FormRequest
    {

        public function rules(): array
        {
            return [
                'uuid' => ['nullable', 'string', 'uuid'],
                'name' => ['required', 'string', 'max:255'],
                'price' => ['required', 'numeric', 'min:0'],
                'currency' => ['required', 'string', 'size:3'],
                'stock_on_hand' => ['nullable', 'integer', 'min:0'],
            ];
        }

        public function messages(): array
        {
            return [
                'name.required' => 'Název je povinný.',
                'price.required' => 'Cena je povinná.',
                'currency.required' => 'Měna je povinná.',
            ];
        }

        protected function prepareForValidation(): void
        {
            if ($this->currency) {
                $this->merge([
                    'currency' => strtoupper($this->currency),
                ]);
            }
        }
    }
