<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttributeOptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'attribute_id' => ['required', 'exists:attributes,id'],
            'value' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'color_hex' => ['nullable', 'string', 'max:7'],
            'image' => ['nullable', 'string', 'max:255'],
        ];
    }
}
