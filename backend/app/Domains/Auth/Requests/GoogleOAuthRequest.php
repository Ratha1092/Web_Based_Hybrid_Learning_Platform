<?php

namespace App\Domains\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GoogleOAuthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'email' => strtolower($this->email),
        ]);
    }

    public function rules(): array
    {
        return [
            'id_token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'name' => ['required', 'string'],
        ];
    }
}