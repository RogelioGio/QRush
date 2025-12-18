<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'table_id' => 'required|integer|exists:tables,id',
            'status' => 'required|string',
            'order_items' => 'array|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'table_id.required' => 'Table ID is required.',
            'table_id.integer' => 'Table ID must be an integer.',
            'table_id.exists' => 'The specified table does not exist.',

            'status.required' => 'Order status is required.',
            'status.string' => 'Order status must be a string.',

            'order_items.array' => 'Order items must be an array.',
            'order_items.min' => 'At least one order item is required.',
        ];
    }
}
