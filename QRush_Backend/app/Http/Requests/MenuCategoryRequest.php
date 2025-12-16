<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MenuCategoryRequest extends FormRequest
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
        $categoryId = $this->route('menu_category')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:menu_categories,name' . ($categoryId ? ',' . $categoryId : ''),
            ],
            'is_active' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The category name is required.',
            'name.string' => 'The category name must be a string.',
            'name.max' => 'The category name may not be greater than 255 characters.',
            'name.unique' => 'The category name has already been taken.',

            'is_active.required' => 'The status is required.',
            'is_active.boolean' => 'The is_active field must be true or false.',
        ];
    }
}
