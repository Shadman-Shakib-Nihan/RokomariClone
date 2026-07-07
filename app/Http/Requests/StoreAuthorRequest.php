<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAuthorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:authors,slug'],
            'biography' => ['nullable', 'string'],
            'photo' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }
}
