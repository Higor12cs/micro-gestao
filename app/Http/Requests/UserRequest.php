<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->user)],
            'password' => 'nullable|string|min:8|confirmed',
            'active' => 'sometimes',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo é obrigatório.',
            'string' => 'O campo deve ser uma string.',
            'max' => 'O campo deve ter no máximo :max caracteres.',
            'email' => 'O campo deve ser um e-mail válido.',
            'unique' => 'O campo informado já está em uso.',
            'min' => 'O campo deve ter no mínimo :min caracteres.',
            'confirmed' => 'As senhas não coincidem.',
        ];
    }
}
