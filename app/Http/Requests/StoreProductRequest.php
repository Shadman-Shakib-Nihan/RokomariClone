<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'publisher_id' => ['nullable', 'exists:publishers,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:products,slug'],
            'description' => ['nullable', 'string'],
            'weight' => ['nullable', 'numeric'],
            'barcode' => ['nullable', 'string', 'max:255'],
            'featured' => ['boolean'],
            'status' => ['required', 'string', 'max:50'],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],

            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,webp', 'max:2048'],

            'authors' => ['nullable', 'array'],
            'authors.*.author_id' => ['required', 'exists:authors,id'],
            'authors.*.sort_order' => ['nullable', 'integer', 'min:0'],

            'attributes' => ['nullable', 'array'],
            'attributes.*.attribute_id' => ['required', 'exists:attributes,id'],
            'attributes.*.attribute_option_id' => ['nullable', 'exists:attribute_options,id'],
            'attributes.*.value_text' => ['nullable', 'string'],
            'attributes.*.value_number' => ['nullable', 'numeric'],
            'attributes.*.value_boolean' => ['nullable', 'boolean'],
            'attributes.*.value_date' => ['nullable', 'date'],

            'variants' => ['nullable', 'array'],
            'variants.*.sku' => ['required', 'string', 'max:255', 'unique:product_variants,sku'],
            'variants.*.price' => ['required', 'numeric', 'min:0'],
            'variants.*.discount_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.stock_quantity' => ['nullable', 'integer', 'min:0'],
            'variants.*.is_default' => ['boolean'],
            'variants.*.attribute_option_ids' => ['nullable', 'array'],
            'variants.*.attribute_option_ids.*' => ['exists:attribute_options,id'],
            'variants.*.images' => ['nullable', 'array'],
            'variants.*.images.*' => ['image', 'mimes:jpeg,png,webp', 'max:2048'],
        ];
    }
}
