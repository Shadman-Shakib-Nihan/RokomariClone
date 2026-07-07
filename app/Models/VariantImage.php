<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
