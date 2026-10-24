<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:150',
            'description' => 'sometimes|nullable|string',
            'category_id' => 'sometimes|required|integer|exists:categories,category_id',
            'price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'img' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_img' => 'sometimes|boolean',
        ];
    }
}