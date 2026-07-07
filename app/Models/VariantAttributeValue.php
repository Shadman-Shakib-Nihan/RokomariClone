<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VariantAttributeValue extends Model
{
    protected $fillable = [
        'product_variant_id',
        'attribute_option_id',
    ];

    /**
     * Product Variant
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(
            ProductVariant::class,
            'product_variant_id'
        );
    }

    /**
     * Selected Attribute Option
     */
    public function option(): BelongsTo
    {
        return $this->belongsTo(
            AttributeOption::class,
            'attribute_option_id'
        );
    }
}
