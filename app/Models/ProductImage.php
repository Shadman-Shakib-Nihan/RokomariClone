<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'url',
        'sort_order',
        'is_primary',
        'alt_text',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /**
     * Product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => match (true) {
                $value === null => null,
                str_starts_with($value, 'http://'),
                str_starts_with($value, 'https://') => $value,
                default => Storage::disk('public')->url($value),
            },
        );
    }
}
