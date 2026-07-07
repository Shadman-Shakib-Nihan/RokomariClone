<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'discount_price',
        'stock_quantity',
        'is_default',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'is_default' => 'boolean',
    ];

    /**
     * Parent Product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Variant Images
     */
    public function images(): HasMany
    {
        return $this->hasMany(VariantImage::class);
    }

    /**
     * Variant Attributes
     */
    public function attributeValues(): HasMany
    {
        return $this->hasMany(VariantAttributeValue::class);
    }
}
