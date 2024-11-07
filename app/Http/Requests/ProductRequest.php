<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:255',
            'section_id' => 'nullable|exists:sections,id',
            'group_id' => 'nullable|exists:groups,id',
            'brand_id' => 'nullable|exists:brands,id',
            'cost_price' => 'required|numeric|gt:0',
            'sale_price' => 'required|numeric|gt:0',
            'minimum_stock' => 'sometimes|numeric',
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
            'numeric' => 'O campo deve ser um número.',
            'gt' => 'O campo deve ser maior que zero.',
            'exists' => 'O valor informado não é válido.',
        ];
    }
}
