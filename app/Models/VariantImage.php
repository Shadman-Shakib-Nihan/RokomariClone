<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class VariantImage extends Model
{
    protected $fillable = [
        'product_variant_id',
        'url',
        'sort_order',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /**
     * Product Variant
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
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
