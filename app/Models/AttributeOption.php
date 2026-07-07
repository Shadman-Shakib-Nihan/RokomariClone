<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttributeOption extends Model
{
    protected $fillable = [
        'attribute_id',
        'value',
        'sort_order',
        'color_hex',
        'image',
    ];

    /**
     * Parent attribute
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    /**
     * Variant values using this option
     */
    public function variantValues(): HasMany
    {
        return $this->hasMany(VariantAttributeValue::class);
    }

    /**
     * Product values using this option
     */
    public function productValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class);
    }
}
