<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAttributeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('attributes', 'slug')->ignore($this->route('attribute'))],
            'input_type' => ['required', 'string', 'max:50', 'in:text,select,boolean,date,number'],
            'unit' => ['nullable', 'string', 'max:255'],
        ];
    }
}
