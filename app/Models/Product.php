<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
    'category_id',
    'brand_id',
    'name',
    'slug',
    'description',
    'weight',
    'barcode',
    'featured',
    'status',
    'published_at',
    'meta_title',
    'meta_description',
];
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;
}
