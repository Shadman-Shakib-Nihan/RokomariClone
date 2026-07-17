<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Author extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'biography',
        'photo',
        'is_active',
    ];

    protected $appends = ['photo_url'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected function photoUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => match (true) {
                ! $this->photo => null,
                str_starts_with($this->photo, 'http://'),
                str_starts_with($this->photo, 'https://') => $this->photo,
                str_starts_with($this->photo, '/') => null,
                default => Storage::disk('public')->url($this->photo),
            },
        );
    }

    /**
     * Books written by this author
     */
    public function productAuthors(): HasMany
    {
        return $this->hasMany(ProductAuthor::class);
    }
}
