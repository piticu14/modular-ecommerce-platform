<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            /**
             * The user's email address.
             *
             * @example user@example.com
             */
            'email' => ['required', 'email'],
            /**
             * The user's password.
             *
             * @example secret123
             */
            'password' => ['required', 'string'],
        ];
    }
}
