<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MenuItemRequest extends FormRequest
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
        $itemId = $this->route('menu_item')?->id;

        return [
            'menu_category_id' => 'required|exists:menu_categories,id',
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:menu_categories,name' . ($itemId ? ',' . $itemId : ''),
            ],
            'price' => 'required|numeric|min:0',
            'is_available' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'menu_category_id.required' => 'The menu category is required.',
            'menu_category_id.exists' => 'The selected menu category does not exist.',

            'name.required' => 'The item name is required.',
            'name.string' => 'The item name must be a string.',
            'name.max' => 'The item name may not be greater than 255 characters.',
            'name.unique' => 'The item name has already been taken.',

            'price.required' => 'The price is required.',
            'price.numeric' => 'The price must be a number.',
            'price.min' => 'The price must be at least 0.',

            'is_available.required' => 'The availability status is required.',
            'is_available.boolean' => 'The is_available field must be true or false.',
        ];
    }
}
