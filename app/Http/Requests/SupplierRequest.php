<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'legal_name' => 'nullable|string|max:255',
            'cpf_cnpj' => ['nullable', Rule::unique('suppliers')->ignore($this->supplier), 'string', 'max:20'],
            'rg' => 'nullable|string|max:20',
            'ie' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'zip_code' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'number' => 'nullable|string|max:10',
            'complement' => 'nullable|string|max:255',
            'neighborhood' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'created_by' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo é obrigatório.',
            'string' => 'O campo deve ser uma string.',
            'max' => 'O campo não pode ter mais que :max caracteres.',
            'unique' => 'O valor informado já está em uso.',
            'email' => 'O campo deve ser um email válido.',
            'date' => 'O campo deve ser uma data válida.',
            'integer' => 'O campo deve ser um número inteiro.',
        ];
    }
}
