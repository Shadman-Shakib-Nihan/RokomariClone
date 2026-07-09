<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Brand extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'description',
        'website',
        'is_active',
    ];

    protected $appends = ['logo_url'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected function logoUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => match (true) {
                ! $this->logo => null,
                str_starts_with($this->logo, 'http://'),
                str_starts_with($this->logo, 'https://') => $this->logo,
                str_starts_with($this->logo, '/') => null,
                default => Storage::disk('public')->url($this->logo),
            },
        );
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'brand_category')
            ->withTimestamps();
    }
}
