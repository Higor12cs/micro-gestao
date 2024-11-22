<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'date' => ['required', 'date'],
            'total_cost' => ['sometimes', 'numeric'],
            'discount' => ['sometimes', 'numeric'],
            'freight' => ['sometimes', 'numeric'],
            'total_price' => ['sometimes', 'numeric'],
            'observation' => ['nullable', 'string'],
            'created_by' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo é obrigatório.',
            'exists' => 'O valor informado é inválido.',
            'date' => 'O valor informado é inválido.',
            'numeric' => 'O valor informado é inválido.',
            'string' => 'O valor informado é inválido.',
        ];
    }
}
