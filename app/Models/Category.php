<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'image',
        'icon',
        'banner',
        'sort_order',
        'is_active',
        'meta_title',
        'meta_description',
    ];

    protected $appends = ['image_url', 'thumbnail_url'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => match (true) {
                ! $this->image => null,
                str_starts_with($this->image, 'http://'),
                str_starts_with($this->image, 'https://') => $this->image,
                str_starts_with($this->image, '/') => null,
                default => Storage::disk('public')->url($this->image),
            },
        );
    }

    protected function thumbnailUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => match (true) {
                ! $this->image => null,
                str_starts_with($this->image, 'http://'),
                str_starts_with($this->image, 'https://') => $this->image,
                str_starts_with($this->image, '/') => null,
                default => Storage::disk('public')->url($this->image),
            },
        );
    }

    /**
     * Parent Category
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Child Categories
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Products under this category
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Attributes assigned to this category
     */
    public function categoryAttributes(): HasMany
    {
        return $this->hasMany(CategoryAttribute::class);
    }
}
