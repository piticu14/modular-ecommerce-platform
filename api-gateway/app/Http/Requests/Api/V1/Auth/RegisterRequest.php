<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
             * The user's full name.
             *
             * @example John Doe
             */
            'name' => ['required', 'string', 'max:255'],
            /**
             * The user's email address.
             *
             * @example user@example.com
             */
            'email' => ['required', 'string', 'email', 'max:255'],
            /**
             * The user's password.
             *
             * @example secret123
             */
            'password' => ['required', 'string', 'min:6'],
        ];
    }
}
