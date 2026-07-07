<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'input_type',
        'unit',
    ];

    /**
     * Dropdown options
     */
    public function options(): HasMany
    {
        return $this->hasMany(AttributeOption::class);
    }

    /**
     * Categories using this attribute
     */
    public function categoryAttributes(): HasMany
    {
        return $this->hasMany(CategoryAttribute::class);
    }

    /**
     * Product values
     */
    public function productValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class);
    }
}
