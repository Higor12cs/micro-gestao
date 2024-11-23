<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch' => 'nullable',
            'account' => 'nullable',
            'name' => 'required|string|max:255',
            'type' => 'required',
            'balance' => 'nullable',
            'active' => 'sometimes',
            'created_by' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo é obrigatório.',
            'string' => 'O campo deve ser uma string.',
            'max' => 'O campo deve ter no máximo :max caracteres.',
            'boolean' => 'O campo deve ser verdadeiro ou falso.',
        ];
    }
}
